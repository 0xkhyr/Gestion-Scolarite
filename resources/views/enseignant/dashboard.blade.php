@extends('layouts.dashboard')

@section('title', __('app.tableau_bord_enseignant'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('app.tableau_de_bord') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-2">Bonjour, {{ $teacher->prenom }} {{ $teacher->nom }}!</h2>
                            <p class="mb-0 opacity-75">
                                Classes: {{ $stats['classe_name'] }} | 
                                Matières: {{ $stats['matieres'] ?? $teacher->matiere }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-20 rounded-circle p-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <span class="fs-2 fw-bold">{{ strtoupper(substr($teacher->prenom, 0, 1)) }}{{ strtoupper(substr($teacher->nom, 0, 1)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <span class="text-primary fs-3 fw-bold">{{ $stats['students_count'] }}</span>
                        </div>
                    </div>
                    <h5 class="fw-bold text-primary mb-1">{{ __('app.etudiants') }}</h5>
                    <p class="text-muted mb-0 small">Total inscrit</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <span class="text-success fs-3 fw-bold">{{ $stats['courses_count'] }}</span>
                        </div>
                    </div>
                    <h5 class="fw-bold text-success mb-1">{{ __('app.cours') }}</h5>
                    <p class="text-muted mb-0 small">Matières enseignées</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <span class="text-warning fs-3 fw-bold">{{ $stats['evaluations_count'] }}</span>
                        </div>
                    </div>
                    <h5 class="fw-bold text-warning mb-1">{{ __('app.evaluations') }}</h5>
                    <p class="text-muted mb-0 small">Examens créés</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <span class="text-info fs-3 fw-bold">{{ \Carbon\Carbon::now()->format('d') }}</span>
                        </div>
                    </div>
                    <h5 class="fw-bold text-info mb-1">{{ \Carbon\Carbon::now()->translatedFormat('F') }}</h5>
                    <p class="text-muted mb-0 small">{{ __('app.aujourdhui') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        {{ __('app.actions_rapides') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('enseignant.mes-etudiants') }}" class="btn btn-outline-primary py-3 fw-medium">
                            {{ __('app.voir_mes_etudiants') }}
                        </a>
                        <a href="{{ route('enseignant.saisir-notes') }}" class="btn btn-outline-success py-3 fw-medium">
                            {{ __('app.saisir_des_notes') }}
                        </a>
                        <a href="{{ route('enseignant.mes-cours') }}" class="btn btn-outline-info py-3 fw-medium">
                            {{ __('app.mon_emploi_du_temps') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Students Preview -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        {{ __('app.mes_etudiants') }} ({{ $students->count() }})
                    </h5>
                    @if($students->count() > 0)
                        <a href="{{ route('enseignant.mes-etudiants') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('app.voir_tous') }}
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($students->count() > 0)
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="border-0">{{ __('app.nom_complet') }}</th>
                                        <th class="border-0">{{ __('app.email') }}</th>
                                        <th class="border-0">{{ __('app.telephone') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students->take(5) as $student)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 14px;">
                                                        {{ strtoupper(substr($student->prenom, 0, 1)) }}
                                                    </div>
                                                    <span class="fw-medium">{{ $student->prenom }} {{ $student->nom }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $student->email }}" class="text-decoration-none">
                                                    {{ $student->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($student->telephone)
                                                    <a href="tel:{{ $student->telephone }}" class="text-decoration-none">
                                                        {{ $student->telephone }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($students->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">... et {{ $students->count() - 5 }} autres étudiants</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">{{ __('app.no_student_assigned') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Evaluations -->
    @if($recentEvaluations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            {{ __('app.evaluations_recentes') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="border-0">{{ __('app.titre') }}</th>
                                        <th class="border-0">{{ __('app.type') }}</th>
                                        <th class="border-0">{{ __('app.cours') }}</th>
                                        <th class="border-0">{{ __('app.date') }}</th>
                                        <th class="border-0">{{ __('app.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvaluations as $evaluation)
                                        <tr>
                                            <td>{{ $evaluation->titre }}</td>
                                            <td>
                                                <span class="badge bg-{{ $evaluation->type == 'examen' ? 'danger' : 'info' }}">
                                                    {{ ucfirst($evaluation->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $evaluation->matiere_name ?? 'N/A' }}</td>
                                            <td>{{ $evaluation->date ? \Carbon\Carbon::parse($evaluation->date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ __('app.actif') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
