@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-semibold"><i class="bi bi-pencil-square me-1"></i> Уреди состанок</h2>

        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye"></i> Преглед
            </a>
            <a href="{{ route('meetings.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('meetings.update', $meeting) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Наслов <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $meeting->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Опис</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description', $meeting->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Почеток <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                               id="start_time" name="start_time"
                               value="{{ old('start_time', optional($meeting->start_time)->format('Y-m-d\\TH:i')) }}" required>
                        @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">Крај</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                               id="end_time" name="end_time"
                               value="{{ old('end_time', optional($meeting->end_time)->format('Y-m-d\\TH:i')) }}">
                        @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Локација</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                           id="location" name="location" value="{{ old('location', $meeting->location) }}">
                    @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">Статус</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        @php $currentStatus = old('status', $meeting->status); @endphp
                        <option value="scheduled" @selected($currentStatus === 'scheduled')>закажан</option>
                        <option value="ongoing" @selected($currentStatus === 'ongoing')>во тек</option>
                        <option value="completed" @selected($currentStatus === 'completed')>завршен</option>
                        <option value="cancelled" @selected($currentStatus === 'cancelled')>откажан</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-success">
                        Зачувај промени
                    </button>

                    <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary">Откажи</a>

                    <span class="flex-grow-1 d-none d-sm-block"></span>

                    <a href="#" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('delete-meeting-form').submit();">
                        <i class="bi bi-trash"></i> Избриши состанок
                    </a>
                </div>
            </form>

            <form id="delete-meeting-form" method="POST" action="{{ route('meetings.destroy', $meeting) }}" class="d-none"
                  onsubmit="return confirm('Дали сте сигурни дека сакате да го избришете состанокот? Ова не може да се врати.');">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection

