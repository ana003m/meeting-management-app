@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">{{ $meeting->title }}</h2>
                        <a href="{{ route('meetings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Назад
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-lg mb-4">Детали</h3>
                                <p><strong>Датум:</strong> {{ $meeting->start_time->format('d.m.Y H:i') }}</p>
                                <p><strong>Локација:</strong> {{ $meeting->location ?? 'Н/А' }}</p>
                                <p><strong>Статус:</strong> {{ $meeting->status }}</p>

                                <h4 class="font-semibold mt-4 mb-2">Учесници</h4>
                                <ul class="list-disc pl-5">
                                    @foreach($meeting->participants as $participant)
                                        <li>{{ $participant->name }}</li>
                                    @endforeach
                                </ul>

                                <h4 class="font-semibold mt-4 mb-2">Агенда</h4>
                                <ol class="list-decimal pl-5">
                                    @foreach($meeting->agendas as $agenda)
                                        <li>{{ $agenda->topic }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Белешки од состанок</h5>
                                    <small class="text-white-50">Внесете ги сите важни информации</small>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('meetings.notes.store', $meeting) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                    <textarea class="form-control"
                              id="content"
                              name="content"
                              rows="20"
                              style="font-family: monospace; font-size: 14px; line-height: 1.6; resize: vertical;"
                              placeholder="=== ВНЕСЕТЕ ГИ ВАШИТЕ БЕЛЕШКИ ОВДЕ ==="

 required>{{ old('content') }}</textarea>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success btn-lg py-3">
                                                <i class="bi bi-send-fill"></i> Зачувај и генерирај записник
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
