<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingMinute;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MeetingController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $userId = auth()->id();
        $scope = request()->query('scope', 'all');

        $query = Meeting::with(['creator', 'latestMinutes'])
            ->orderBy('start_time', 'desc');

        if ($scope === 'created') {
            $query->where('created_by', $userId);
        } elseif ($scope === 'participating') {
            $query->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        } else {
            // all: created OR participating
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhereHas('participants', function ($qq) use ($userId) {
                        $qq->where('user_id', $userId);
                    });
            });
        }

        $meetings = $query->paginate(10)->withQueryString();

        return view('meetings.index', compact('meetings', 'scope'));
    }

    public function create()
    {
        return view('meetings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'agenda_text' => 'nullable|string',
        ]);

        $meeting = Meeting::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'created_by' => auth()->id(),
            'status' => 'scheduled',
        ]);

        if (!empty($validated['participants'])) {
            $meeting->participants()->attach($validated['participants']);
        }


        if (!empty($validated['agenda_text'])) {
            $lines = explode("\n", $validated['agenda_text']);
            $order = 0;
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $topic = preg_replace('/^\d+\.\s*/', '', $line);
                    $meeting->agendas()->create([
                        'topic' => $topic,
                        'description' => null,
                        'order' => $order,
                    ]);
                    $order++;
                }
            }
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Состанокот е успешно креиран.');
    }

    public function show(Meeting $meeting)
    {
        if ($meeting->created_by !== auth()->id() && !$meeting->participants->contains('id', auth()->id())) {
            abort(403, 'Немате пристап до овој состанок.');
        }

        $meeting->load(['agendas', 'participants', 'notes' => function($query) {
            $query->latest()->limit(5);
        }]);

        return view('meetings.show', compact('meeting'));
    }

    public function showMinutes(Meeting $meeting, MeetingMinute $minutes)
    {
        if ($minutes->meeting_id !== $meeting->id) {
            abort(404);
        }

        if ($meeting->created_by !== auth()->id() && !$meeting->participants->contains('id', auth()->id())) {
            abort(403, 'Немате пристап до овој записник.');
        }

        return view('meetings.minutes', compact('meeting', 'minutes'));
    }

    public function minutesIndex(Meeting $meeting)
    {
        if ($meeting->created_by !== auth()->id() && !$meeting->participants->contains('id', auth()->id())) {
            abort(403, 'Немате пристап до овој состанок.');
        }

        $minutes = $meeting->minutes()
            ->with(['generator', 'sourceNote'])
            ->orderByDesc('generated_at')
            ->get();

        return view('meetings.minutes-index', compact('meeting', 'minutes'));
    }

    public function edit(Meeting $meeting)
    {
        if ($meeting->created_by !== auth()->id()) {
            abort(403, 'Само креаторот може да го уредува состанокот.');
        }

        return view('meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->created_by !== auth()->id()) {
            abort(403, 'Само креаторот може да го ажурира состанокот.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:scheduled,ongoing,completed,cancelled',
        ]);

        $meeting->update($validated);

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Состанокот е успешно ажуриран.');
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->created_by !== auth()->id()) {
            abort(403, 'Само креаторот може да го избрише состанокот.');
        }

        $meeting->delete();

        return redirect()->route('meetings.index')
            ->with('success', 'Состанокот е успешно избришан.');
    }
}
