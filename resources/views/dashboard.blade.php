@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-semibold"><i class="bi bi-speedometer2 me-1"></i> Почетна</h2>
            <div class="text-muted">Добредојде назад, <span class="fw-semibold">{{ Auth::user()->name }}</span>.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meetings.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Нов состанок
            </a>
            <a href="{{ route('meetings.index') }}" class="btn btn-primary">
                <i class="bi bi-list"></i> Сите состаноци
            </a>
        </div>
    </div>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Вкупно состаноци</div>
                            <div class="display-6 fw-semibold mb-0">{{ $totalMeetingsCount ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background: rgba(13,110,253,.12);">
                            <i class="bi bi-collection fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('meetings.index') }}" class="text-decoration-none">Преглед <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Креирани од мене</div>
                            <div class="display-6 fw-semibold mb-0">{{ $createdCount ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background: rgba(25,135,84,.12);">
                            <i class="bi bi-person-badge fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Состаноци што ги имате креирано.</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Каде сум вклучен</div>
                            <div class="display-6 fw-semibold mb-0">{{ $participatingCount ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background: rgba(13,202,240,.12);">
                            <i class="bi bi-people fs-4 text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Состаноци каде што сте учесник.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Завршени</div>
                            <div class="display-6 fw-semibold mb-0">{{ $completedCount ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background: rgba(25,135,84,.12);">
                            <i class="bi bi-check2-circle fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Состаноци што сте ги креирале и се означени како завршени.</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Претстојни</div>
                            <div class="display-6 fw-semibold mb-0">{{ $upcomingCount ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background: rgba(255,193,7,.18);">
                            <i class="bi bi-clock-history fs-4 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small">Закажани во иднина.</div>
                </div>
            </div>
        </div>
    </div>
@endsection
