@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-semibold">{{ $meeting->title }}</h2>
            <div class="text-muted">
                <i class="bi bi-calendar3"></i> {{ $meeting->start_time->format('d.m.Y H:i') }}
                @if($meeting->location)
                    <span class="mx-2">•</span>
                    <i class="bi bi-geo-alt"></i> {{ $meeting->location }}
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($meeting->created_by === auth()->id())
                <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil"></i> Уреди
                </a>
                <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" class="d-inline"
                      onsubmit="return confirm('Дали сте сигурни дека сакате да го избришете состанокот? Ова не може да се врати.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> Избриши
                    </button>
                </form>
            @endif

            @if($meeting->minutes()->exists())
                <a href="{{ route('meetings.minutes.index', $meeting) }}" class="btn btn-outline-success">
                    <i class="bi bi-journal-text"></i> Записници
                </a>
            @endif

            <a href="{{ route('meetings.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
        </div>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-semibold">Детали</h5>
                        @php
                            $badge = 'secondary';
                            if ($meeting->status === 'completed') $badge = 'success';
                            elseif ($meeting->status === 'scheduled') $badge = 'warning';

                            $statusLabel = config('meeting.status_labels.' . $meeting->status, $meeting->status);
                        @endphp
                        <span class="badge text-bg-{{ $badge }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="small text-muted">Учесници</div>
                    @if($meeting->participants->count() > 0)
                        <ul class="list-unstyled mb-3 mt-2">
                            @foreach($meeting->participants as $participant)
                                <li class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-person text-muted"></i>
                                    <span>{{ $participant->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted mb-3">Нема учесници.</div>
                    @endif

                    <div class="small text-muted">Агенда</div>
                    @if($meeting->agendas->count() > 0)
                        <ol class="mb-0 mt-2">
                            @foreach($meeting->agendas as $agenda)
                                <li class="mb-1">{{ $agenda->topic }}</li>
                            @endforeach
                        </ol>
                    @else
                        <div class="text-muted mt-2">Нема агенда.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-1 fw-semibold"><i class="bi bi-pencil-square"></i> Белешки од состанок</h5>
                            <div class="text-muted small">Внесете ги сите важни информации за да се генерира записник.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('meetings.notes.store', $meeting) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" id="content" name="content" rows="18"
                                      style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size: 14px; line-height: 1.6; resize: vertical;"
                                      placeholder="=== ВНЕСЕТЕ ГИ ВАШИТЕ БЕЛЕШКИ ОВДЕ ===" required>{{ old('content') }}</textarea>
                        </div>

                        <div class="d-grid d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success btn-lg">
                                Зачувај и генерирај записник
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
