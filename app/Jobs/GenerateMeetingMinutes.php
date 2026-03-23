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

        Врати JSON објект со следната структура:
        {
            "summary": "Концизно резиме на целиот состанок (максимум 200 збора)",
            "action_items": [
                "Акциона точка 1 - што треба да се направи и кој е одговорен",
                "Акциона точка 2 - што треба да се направи и кој е одговорен"
            ],
            "decisions": [
                "Донесена одлука 1",
                "Донесена одлука 2"
            ]
        }

        Ако нема акциони точки или одлуки, врати празни листи.
        EOT;
    }
}
