<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $meetingsCount = Meeting::where('created_by', auth()->id())->count();
        $completedCount = Meeting::where('created_by', auth()->id())
            ->where('status', 'completed')
            ->count();
        $upcomingCount = Meeting::where('created_by', auth()->id())
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->count();

        return view('dashboard', compact('meetingsCount', 'completedCount', 'upcomingCount'));
    }
}
