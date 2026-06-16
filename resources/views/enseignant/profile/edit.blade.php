@extends('layouts.dashboard')

@section('title', __('app.mon_profil'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('app.mon_enseignement') }}</li>
    <li class="breadcrumb-item active">{{ __('app.mon_profil') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle text-primary"></i> {{ __('app.mon_profil') }}
        </h1>
        <div class="text-muted">
            <i class="fas fa-clock me-1"></i>
            {{ __('app.derniere_connexion') }}: {{ $user->updated_at->diffForHumans() }}
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>{{ __('app.informations_personnelles') }}
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enseignant.profil.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenom" class="form-label fw-bold">
                                        <i class="fas fa-user text-primary me-1"></i>{{ __('app.prenom') }}
                                    </label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" value="{{ old('prenom', $teacher->prenom) }}" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label fw-bold">
                                        <i class="fas fa-user text-primary me-1"></i>{{ __('app.nom') }}
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $teacher->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope text-info me-1"></i>{{ __('app.email') }}
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $teacher->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label fw-bold">
                                        <i class="fas fa-phone text-success me-1"></i>{{ __('app.telephone') }}
                                    </label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $teacher->telephone) }}" required>
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <h6 class="font-weight-bold text-secondary mb-3">
                            <i class="fas fa-lock me-2"></i>{{ __('app.changer_mot_de_passe') }}
                            <small class="text-muted">({{ __('app.optionnel') }})</small>
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('app.nouveau_mot_de_passe') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('app.mot_de_passe_min_8') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">{{ __('app.confirmer_mot_de_passe') }}</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" minlength="8">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('enseignant.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>{{ __('app.retour') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>{{ __('app.enregistrer_modifications') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Statistics & Info -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>{{ __('app.mes_statistiques') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i class="fas fa-users text-primary fs-2"></i>
                                <div class="fw-bold text-primary mt-2">{{ $stats['total_students'] }}</div>
                                <small class="text-muted">{{ __('app.etudiants') }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-chalkboard text-success fs-2"></i>
                                <div class="fw-bold text-success mt-2">{{ $stats['total_classes'] }}</div>
                                <small class="text-muted">{{ __('app.classes') }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <i class="fas fa-book text-info fs-2"></i>
                                <div class="fw-bold text-info mt-2">{{ $stats['total_matieres'] }}</div>
                                <small class="text-muted">{{ __('app.matieres') }}</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i class="fas fa-clipboard-check text-warning fs-2"></i>
                                <div class="fw-bold text-warning mt-2">{{ $stats['total_evaluations'] }}</div>
                                <small class="text-muted">{{ __('app.evaluations') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <div class="bg-secondary bg-opacity-10 rounded p-3">
                            <i class="fas fa-star text-secondary fs-3"></i>
                            <div class="fw-bold text-secondary mt-2">{{ $stats['recent_notes'] }}</div>
                            <small class="text-muted">{{ __('app.notes_cette_semaine') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teaching Assignments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-graduation-cap me-2"></i>{{ __('app.mes_affectations') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($teacherAssignments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($teacherAssignments->unique(function($assignment) { return $assignment->nom_classe . '_' . $assignment->code_matiere; }) as $assignment)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-primary">{{ $assignment->nom_classe }}</strong>
                                            <div class="small text-muted">
                                                <i class="fas fa-book me-1"></i>{{ $assignment->nom_matiere }}
                                            </div>
                                        </div>
                                        <div class="badge bg-light text-dark">
                                            {{ $assignment->code_matiere }}
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ __('app.depuis') }}: {{ \Carbon\Carbon::parse($assignment->date_assignation)->format('d/m/Y') }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-info-circle mb-2"></i>
                            <div>{{ __('app.aucune_affectation') }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Info -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>{{ __('app.informations_compte') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('app.role') }}:</span>
                            <span class="badge bg-success">{{ __('app.enseignant') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('app.status') }}:</span>
                            <span class="badge bg-success">{{ __('app.actif') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('app.membre_depuis') }}:</span>
                            <span>{{ $teacher->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('app.derniere_modification') }}:</span>
                            <span>{{ $teacher->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-opacity-10 {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}
.bg-success.bg-opacity-10 {
    background-color: rgba(var(--bs-success-rgb), 0.1) !important;
}
.bg-info.bg-opacity-10 {
    background-color: rgba(var(--bs-info-rgb), 0.1) !important;
}
.bg-warning.bg-opacity-10 {
    background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
}
.bg-secondary.bg-opacity-10 {
    background-color: rgba(var(--bs-secondary-rgb), 0.1) !important;
}
</style>

<script>
// Password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (password && confirmation && password !== confirmation) {
        this.setCustomValidity('{{ __("app.mots_de_passe_different") }}');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// Show/hide password fields
document.getElementById('password').addEventListener('input', function() {
    const confirmField = document.getElementById('password_confirmation');
    if (this.value.length > 0) {
        confirmField.required = true;
    } else {
        confirmField.required = false;
        confirmField.value = '';
    }
});
</script>
@endsection
