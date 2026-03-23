@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-file-text"></i> Записник од состанок: {{ $meeting->title }}</h4>
                    <div>
                        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-light btn-sm me-2">
                            <i class="bi bi-arrow-left"></i> Назад кон состанок
                        </a>
                        <button onclick="window.print()" class="btn btn-light btn-sm">
                            <i class="bi bi-printer"></i> Печати
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Датум на состанок:</strong> {{ $meeting->start_time->format('d.m.Y H:i') }}</p>
                            <p><strong>Локација:</strong> {{ $meeting->location ?? 'Н/А' }}</p>
                            <p><strong>Креирано од:</strong> {{ $meeting->creator->name ?? 'Н/А' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Генерирано на:</strong> {{ $minutes->generated_at->format('d.m.Y H:i:s') }}</p>
                            <p><strong>Генерирано од:</strong> {{ $minutes->generator->name ?? 'Н/А' }}</p>
                            <p><strong>Врз основа на белешки од:</strong> {{ $minutes->sourceNote->user->name ?? 'Н/А' }}</p>
                        </div>
                    </div>

                    @if($meeting->agendas->count() > 0)
                        <hr>
                        <h5 class="text-primary">Агенда</h5>
                        <ol class="list-group list-group-numbered mb-4">
                            @foreach($meeting->agendas as $agenda)
                                <li class="list-group-item">
                                    <strong>{{ $agenda->topic }}</strong>
                                    @if($agenda->description)
                                        <br><small class="text-muted">{{ $agenda->description }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    <hr>
                    <h5 class="text-primary">Резиме</h5>
                    <div class="alert alert-light border mb-4 p-4">
                        {{ $minutes->summary }}
                    </div>

                    <h5 class="text-primary">Акциони точки</h5>
                    @if(count($minutes->action_items) > 0)
                        <div class="list-group mb-4">
                            @foreach($minutes->action_items as $index => $item)
                                <div class="list-group-item">
                                    <div class="d-flex">
                                        <span class="badge bg-success me-2">{{ $index + 1 }}</span>
                                        <span>{{ $item }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle"></i> Нема акциони точки
                        </div>
                    @endif

                    @if($minutes->decisions && count($minutes->decisions) > 0)
                        <h5 class="text-primary">Донесени одлуки</h5>
                        <div class="list-group mb-4">
                            @foreach($minutes->decisions as $index => $decision)
                                <div class="list-group-item">
                                    <div class="d-flex">
                                        <span class="badge bg-info me-2">{{ $index + 1 }}</span>
                                        <span>{{ $decision }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($minutes->sourceNote)
                        <hr>
                        <h5 class="text-primary">Оригинални белешки</h5>
                        <div class="alert alert-secondary">
                            <p class="mb-0"><small>{{ $minutes->sourceNote->content }}</small></p>
                            <p class="mt-2 mb-0 text-muted"><small>Внесено од: {{ $minutes->sourceNote->user->name }} на {{ $minutes->sourceNote->created_at->format('d.m.Y H:i') }}</small></p>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    <small>Ова е автоматски генериран записник од апликацијата за управување со состаноци.</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {
            .navbar, .btn, .card-footer, footer {
                display: none !important;
            }
            body {
                background-color: white;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush
