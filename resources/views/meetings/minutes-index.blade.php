@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-semibold"><i class="bi bi-journal-text me-1"></i> Записници</h2>
            <div class="text-muted">Состанок: <span class="fw-semibold">{{ $meeting->title }}</span></div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Назад кон состанок
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($minutes->count() === 0)
                <div class="text-center py-5">
                    <div class="mb-3"><i class="bi bi-file-earmark-x fs-1 text-muted"></i></div>
                    <h5 class="fw-semibold">Нема генерирани записници</h5>
                    <p class="text-muted mb-0">Внесете белешки и генерирајте записник од страницата на состанокот.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Генерирано</th>
                            <th>Генерирано од</th>
                            <th class="text-end"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($minutes as $minute)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ optional($minute->generated_at)->format('d.m.Y H:i') ?? 'Н/А' }}</div>
                                </td>
                                <td>
                                    {{ $minute->generator->name ?? 'Н/А' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('meetings.minutes.show', ['meeting' => $meeting, 'minutes' => $minute->id]) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-file-text"></i> Отвори
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

