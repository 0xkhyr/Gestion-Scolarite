@extends('layouts.dashboard')

@section('title', __('app.student_dashboard'))

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ __('Tableau de Bord') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-2">Bonjour, {{ $student->prenom }} {{ $student->nom }}!</h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-geo-alt me-1"></i>
                                Classe: {{ $stats['classe_name'] }} | 
                                <i class="bi bi-bar-chart me-1"></i>
                                Niveau: {{ $stats['level'] }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="bi bi-mortarboard fs-1"></i>
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
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-award-fill text-primary fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">{{ $stats['total_notes'] }}</h3>
                    <p class="text-muted mb-0">Notes</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-graph-up text-success fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $stats['average_grade'] }}</h3>
                    <p class="text-muted mb-0">Moyenne</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clipboard-check-fill text-warning fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $stats['recent_evaluations'] }}</h3>
                    <p class="text-muted mb-0">Évaluations</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-calendar3 text-info fs-2"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ \Carbon\Carbon::now()->format('d/m') }}</h3>
                    <p class="text-muted mb-0">Aujourd'hui</p>
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
                        <i class="bi bi-lightning-charge me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('etudiant.mes-notes') }}" class="btn btn-outline-primary">
                            <i class="bi bi-award me-2"></i>
                            Voir mes notes
                        </a>
                        <a href="{{ route('etudiant.mon-emploi') }}" class="btn btn-outline-success">
                            <i class="bi bi-calendar3 me-2"></i>
                            Mon emploi du temps
                        </a>
                        <a href="{{ route('rechercher-notes') }}" class="btn btn-outline-info">
                            <i class="bi bi-search me-2"></i>
                            Recherche publique
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notes -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-award me-2"></i>
                        Mes Notes Récentes ({{ $notes->count() }})
                    </h5>
                    @if($notes->count() > 0)
                        <a href="{{ route('etudiant.mes-notes') }}" class="btn btn-sm btn-outline-primary">
                            Voir toutes
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($notes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Matière</th>
                                        <th>Évaluation</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes->take(5) as $note)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $note->cours->matiere ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $note->evaluation->titre ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $note->note >= 10 ? 'success' : 'danger' }} fs-6">
                                                    {{ $note->note }}/20
                                                </span>
                                            </td>
                                            <td>{{ $note->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($notes->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">... et {{ $notes->count() - 5 }} autres notes</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-award text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Aucune note disponible pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Evaluations -->
    @if($recentEvaluations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Évaluations Prochaines
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Matière</th>
                                        <th>Date</th>
                                        <th>Statut</th>
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
                                                <span class="badge bg-warning">À venir</span>
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
