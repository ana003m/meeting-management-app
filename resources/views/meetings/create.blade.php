@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Креирај нов состанок</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('meetings.store') }}" id="meeting-form">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Наслов <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Опис</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Почеток <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                               id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">Крај</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                               id="end_time" name="end_time" value="{{ old('end_time') }}">
                        @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Локација</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                           id="location" name="location" value="{{ old('location') }}">
                    @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label class="form-label">Учесници</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @php $users = \App\Models\User::where('id', '!=', auth()->id())->get(); @endphp
                        @if($users->count() > 0)
                            @foreach($users as $user)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           name="participants[]"
                                           value="{{ $user->id }}"
                                           id="user_{{ $user->id }}"
                                        {{ in_array($user->id, old('participants', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                        <strong>{{ $user->name }}</strong> ({{ $user->email }})
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">Нема други регистрирани корисници. <a href="/register">Регистрирај нов</a></p>
                        @endif
                    </div>
                    @error('participants')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
                <hr>
                <h5>Агенда</h5>
                <div class="mb-3">
    <textarea class="form-control @error('agenda_text') is-invalid @enderror"
              name="agenda_text"
              rows="6"
              placeholder="Внесете ја агендата. Секоја нова точка во нов ред."

                   >{{ old('agenda_text') }}</textarea>

                    @error('agenda_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                         Креирај состанок
                    </button>
                    <a href="{{ route('meetings.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> Откажи
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let agendaCounter = document.querySelectorAll('.agenda-item').length;

            function addAgenda() {
                const container = document.getElementById('agenda-items');
                const newRow = document.createElement('div');
                newRow.className = 'row agenda-item mb-2';
                newRow.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="agendas[${agendaCounter}][topic]" placeholder="Тема">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="agendas[${agendaCounter}][description]" placeholder="Опис (опционално)">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger" onclick="removeAgenda(this)">
                            <i class="bi bi-dash"></i> Избриши
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                agendaCounter++;
            }

            function removeAgenda(button) {
                const row = button.closest('.agenda-item');
                if (row) {
                    row.remove();
                }
            }


            document.addEventListener('DOMContentLoaded', function() {
                const addBtn = document.getElementById('add-agenda-btn');
                if (addBtn) {
                    addBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        addAgenda();
                    });
                }
            });
        </script>
    @endpush
@endsection
