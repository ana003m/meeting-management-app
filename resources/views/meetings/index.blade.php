@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div>
            <h2 class="mb-1 fw-semibold"><i class="bi bi-calendar3 me-1"></i> Мои состаноци</h2>
            <div class="text-muted">Преглед и управување со сите состаноци.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meetings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Нов состанок
            </a>
        </div>
    </div>

    @php
        $activeScope = $scope ?? request('scope', 'all');
    @endphp
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a class="nav-link @if($activeScope === 'all') active @endif" href="{{ route('meetings.index', ['scope' => 'all']) }}">Сите</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($activeScope === 'created') active @endif" href="{{ route('meetings.index', ['scope' => 'created']) }}">Креирани од мене</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($activeScope === 'participating') active @endif" href="{{ route('meetings.index', ['scope' => 'participating']) }}">Каде сум вклучен</a>
        </li>
    </ul>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($meetings->count() === 0)
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-calendar-x fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-semibold">Немате состаноци</h5>
                    <p class="text-muted mb-4">Креирајте нов состанок за да започнете.</p>
                    <a href="{{ route('meetings.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Креирај состанок
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Наслов</th>
                            <th>Датум</th>
                            <th>Креирано од</th>
                            <th>Статус</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($meetings as $meeting)
                            <tr>
                                <td class="fw-semibold">{{ $meeting->title }}</td>
                                <td>
                                    <div>{{ $meeting->start_time->format('d.m.Y H:i') }}</div>
                                    @if($meeting->location)
                                        <div class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $meeting->location }}</div>
                                    @endif
                                </td>
                                <td>{{ $meeting->creator->name ?? 'Н/А' }}</td>
                                <td>
                                    @php
                                        $badge = 'secondary';
                                        if ($meeting->status === 'completed') $badge = 'success';
                                        elseif ($meeting->status === 'scheduled') $badge = 'warning';

                                        $statusLabel = config('meeting.status_labels.' . $meeting->status, $meeting->status);
                                    @endphp
                                    <span class="badge text-bg-{{ $badge }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i> Преглед
                                        </a>

                                        @if($meeting->created_by === auth()->id())
                                            <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-pencil"></i> Уреди
                                            </a>
                                        @endif

                                        @if($meeting->latestMinutes)
                                            <a href="{{ route('meetings.minutes.index', $meeting) }}" class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-journal-text"></i> Записници
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $meetings->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
