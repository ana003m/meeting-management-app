<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingNote;
use App\Jobs\GenerateMeetingMinutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MeetingNoteController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Meeting $meeting)
    {
        if ($meeting->created_by !== Auth::id() && !$meeting->participants->contains('id', Auth::id())) {
            abort(403, 'Немате пристап до овој состанок.');
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $note = $meeting->notes()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_final' => true,
        ]);

        GenerateMeetingMinutes::dispatch($meeting->id, $note->id, Auth::id());

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Белешките се зачувани. Записникот се генерира во позадина и ќе биде испратен по е-пошта.');
    }

    public function generate(Request $request, Meeting $meeting, MeetingNote $note)
    {
        if ($meeting->created_by !== Auth::id() && !$meeting->participants->contains('id', Auth::id())) {
            abort(403, 'Немате пристап до овој состанок.');
        }

        if ($note->meeting_id !== $meeting->id) {
            abort(404);
        }


        GenerateMeetingMinutes::dispatch($meeting->id, $note->id, Auth::id());

        return back()->with('success', 'Генерирањето на записникот е рестартирано.');
    }
}
