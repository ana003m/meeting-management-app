@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard</h4>
                </div>
                <div class="card-body">
                    <h5>Welcome, {{ Auth::user()->name }}!</h5>
                    <p>This is your meeting management dashboard.</p>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-calendar"></i> Meetings</h5>
                                    <p class="display-6">{{ $meetingsCount ?? 0 }}</p>
                                    <a href="{{ route('meetings.index') }}" class="text-white">View all →</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-check-circle"></i> Completed</h5>
                                    <p class="display-6">{{ $completedCount ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-clock"></i> Upcoming</h5>
                                    <p class="display-6">{{ $upcomingCount ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('meetings.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Create New Meeting
                        </a>
                        <a href="{{ route('meetings.index') }}" class="btn btn-primary">
                            <i class="bi bi-list"></i> View All Meetings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
