<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Models\MeetingNote;
use App\Models\MeetingMinute;
use App\Mail\MeetingMinutesMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;

class GenerateMeetingMinutes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected $meetingId;
    protected $noteId;
    protected $userId;

    public function __construct($meetingId, $noteId, $userId)
    {
        $this->meetingId = $meetingId;
        $this->noteId = $noteId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            $meeting = Meeting::with('participants')->findOrFail($this->meetingId);
            $note = MeetingNote::findOrFail($this->noteId);

            if (MeetingMinute::where('generated_from_note_id', $note->id)->exists()) {
                Log::info('Записникот веќе е генериран', ['note_id' => $note->id]);
                return;
            }

            $response = $this->callOpenAI($note->content, $meeting);

            $minutes = MeetingMinute::create([
                'meeting_id' => $meeting->id,
                'generated_from_note_id' => $note->id,
                'generated_by' => $this->userId,
                'summary' => $response['summary'],
                'action_items' => $response['action_items'],
                'decisions' => $response['decisions'] ?? [],
                'generated_at' => now(),
            ]);

            foreach ($meeting->participants as $participant) {
                Mail::to($participant->email)->queue(new MeetingMinutesMail($meeting, $minutes));
            }

            Log::info('Успешно генериран записник', ['meeting_id' => $meeting->id]);

        } catch (\Exception $e) {
            Log::error('Грешка при генерирање записник: ' . $e->getMessage(), [
                'meeting_id' => $this->meetingId,
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }


    private function getOpenAIClient()
    {
        return \OpenAI::factory()
            ->withApiKey(env('OPENAI_API_KEY'))
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 30]))
            ->make();
    }

    private function callOpenAI($notes, $meeting)
    {
        $prompt = $this->buildPrompt($notes, $meeting);

        Log::info('Праќам барање до OpenAI', ['prompt_length' => strlen($prompt)]);

        try {
            $client = \OpenAI::factory()
                ->withApiKey(env('OPENAI_API_KEY'))
                ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 30]))
                ->make();

            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ти си асистент кој креира професионални записници од состаноци. Одговорот мора да биде во валиден JSON формат.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);

            $content = $result->choices[0]->message->content;
            Log::info('OpenAI одговор', ['content' => $content]);

            $parsed = json_decode($content, true);

            return [
                'summary' => $parsed['summary'] ?? 'Не можеше да се генерира резиме.',
                'action_items' => $parsed['action_items'] ?? [],
                'decisions' => $parsed['decisions'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI грешка: ' . $e->getMessage());
            throw $e;
        }
    }
    private function buildPrompt($notes, $meeting)
    {
        $agendaText = '';
        if ($meeting->agendas->count() > 0) {
            $agendaItems = $meeting->agendas->map(function($agenda) {
                return "- {$agenda->topic}" . ($agenda->description ? ": {$agenda->description}" : '');
            })->implode("\n");
            $agendaText = "\n\nАгенда на состанокот:\n{$agendaItems}";
        }

        return <<<EOT
        Ве молиме креирај записник од состанок врз основа на следните белешки.

        Наслов на состанок: {$meeting->title}
        Датум: {$meeting->start_time->format('d.m.Y H:i')}{$agendaText}

        Белешки од состанокот:
        {$notes}

        Важно:
        - "summary" мора да биде ОПШТО (executive-level) резиме: теми, цели, контекст, напредок, ризици и отворени прашања.
        - Во "summary" НЕ вклучувај акциони точки, задачи, чек-листи, ниту формулации од типот "X ќе направи Y".
        - Во "summary" по правило не наведувај имиња/одговорности; имиња користи само ако се критични за разбирање (на пр. надворешен клиент).
        - "action_items" треба да бидат детални и практични. Секоја ставка да опишува: што се прави + кој е одговорен + (ако постои) рок/датум + краток критериум за „готово“.
          Ако во белешките НЕ е наведено кој/кога, НЕ измислувај — користи "(одговорен: не е наведено)" и/или "(рок: не е наведен)".
        - "decisions" да содржи само јасно донесени одлуки (не задачи). Не дуплирај ист текст во "action_items".

        Врати JSON објект со следната структура (секогаш со сите клучеви):
        {
            "summary": "Општо резиме на состанокот (120–180 збора), без задачи/одговорности",
            "action_items": [
                "Акциона точка 1 - што точно треба да се направи и критериум за готово",
                "Акциона точка 2 - што точно треба да се направи и критериум за готово"
            ],
            "decisions": [
                "Донесена одлука 1 (конкретно што е одлучено)",
                "Донесена одлука 2 (конкретно што е одлучено)"
            ]
        }

        Ако нема акциони точки или одлуки, врати празни листи.

        Формат:
        - ВРАТИ САМО валиден JSON (без markdown, без ``` блокови, без дополнителен текст).
        - Користи двојни наводници за клучеви и стрингови.
        EOT;
    }
}
