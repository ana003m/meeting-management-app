<?php

namespace App\Http\Controllers;

use App\Models\Meeting;

class HomeController extends Controller
{

    public function index()
    {
        $userId = auth()->id();

        $createdCount = Meeting::where('created_by', $userId)->count();

        $participatingCount = Meeting::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        // Total unique meetings the user can access (created OR participating)
        $totalMeetingsCount = Meeting::query()
            ->where('created_by', $userId)
            ->orWhereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        // Keep existing breakdowns (based on meetings created by the user)
        $completedCount = Meeting::where('created_by', $userId)
            ->where('status', 'completed')
            ->count();

        $upcomingCount = Meeting::where('created_by', $userId)
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->count();

        return view('dashboard', compact(
            'totalMeetingsCount',
            'createdCount',
            'participatingCount',
            'completedCount',
            'upcomingCount'
        ));
    }
}
