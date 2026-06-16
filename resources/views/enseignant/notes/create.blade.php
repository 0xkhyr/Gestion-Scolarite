@extends('layouts.dashboard')

@section('title', __('app.saisir_notes'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('app.gestion_academique') }}</li>
    <li class="breadcrumb-item active">{{ __('app.saisir_notes') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            {{ __('app.saisir_notes') }}
        </h1>
        @if($selectedClass)
            <div class="badge bg-primary fs-6">
                {{ $selectedClass->nom_classe }}
            </div>
        @endif
    </div>

    @if($teacherClasses->count() > 0)
        <!-- Class Selector -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <span class="fw-semibold text-muted">{{ __('app.choisir_classe') }}:</span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap" style="max-height: 150px; overflow-y: auto;">
                                @foreach($teacherClasses as $classe)
                                    <a href="{{ request()->fullUrlWithQuery(['classe_id' => $classe->id_classe]) }}" 
                                       class="btn {{ $selectedClass && $selectedClass->id_classe == $classe->id_classe ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                        {{ $classe->nom_classe }}
                                        <span class="badge bg-light text-dark ms-1">{{ $classe->etudiants->count() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($selectedClass)
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('app.total_etudiants') }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $students->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('app.evaluations') }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $evaluations->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('app.notes_saisies') }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ \App\Models\Note::whereHas('etudiant', function($q) use ($selectedClass) { 
                                            $q->where('id_classe', $selectedClass->id_classe); 
                                        })->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('app.moyenne_classe') }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        @php
                                            $classAvg = \App\Models\Note::whereHas('etudiant', function($q) use ($selectedClass) { 
                                                $q->where('id_classe', $selectedClass->id_classe); 
                                            })->avg('note');
                                        @endphp
                                        {{ $classAvg ? number_format($classAvg, 1) : '--' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Students List -->
                <div class="col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
                                    {{ __('app.etudiants') }} - {{ $selectedClass->nom_classe }}
                                </h6>
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" class="form-control form-control-sm" id="searchStudent" 
                                           placeholder="{{ __('app.rechercher') }}...">
                                    <div class="input-group-append">
                                        <span class="input-group-text">🔍</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="max-height: 650px; overflow-y: auto; overflow-x: hidden;">
                            @if($students->count() > 0)
                                <div class="list-group list-group-flush" id="studentsList">
                                    @foreach($students as $student)
                                        <div class="list-group-item list-group-item-action student-item py-3" 
                                             data-student="{{ strtolower($student->prenom . ' ' . $student->nom) }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="d-flex align-items-start flex-grow-1">
                                                    <div class="avatar-circle bg-primary text-white me-3 flex-shrink-0">
                                                        {{ strtoupper(substr($student->prenom, 0, 1) . substr($student->nom, 0, 1)) }}
                                                    </div>
                                                    <div class="flex-grow-1 min-width-0">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <strong class="text-truncate me-2">{{ $student->prenom }} {{ $student->nom }}</strong>
                                                            <button class="btn btn-sm btn-outline-primary flex-shrink-0" 
                                                                    onclick="selectStudent({{ $student->id_etudiant }}, '{{ $student->prenom }} {{ $student->nom }}', '{{ $student->matricule }}')">
                                                                {{ __('app.noter') }}
                                                            </button>
                                                        </div>
                                                        <div class="text-muted small mb-2">
                                                            {{ $student->matricule }}
                                                        </div>
                                                        @php
                                                            $studentNotesCount = \App\Models\Note::where('id_etudiant', $student->id_etudiant)->count();
                                                            $studentAvg = \App\Models\Note::where('id_etudiant', $student->id_etudiant)->avg('note');
                                                        @endphp
                                                        <div class="d-flex align-items-center text-info small">
                                                            <span class="me-3">{{ $studentNotesCount }} {{ __('app.notes') }}</span>
                                                            @if($studentAvg)
                                                                <span class="badge bg-light text-dark">Moy: {{ number_format($studentAvg, 1) }}</span>
                                                            @endif
                                                        </div>
                                        @if(isset($existingNotes[$student->id_etudiant]) && $existingNotes[$student->id_etudiant]->count() > 0)
                                            <div class="mt-2">
                                                <div class="text-success small fw-bold mb-1">
                                                    {{ __('app.notes_existantes') }}:
                                                </div>
                                                <div class="d-flex flex-wrap gap-1" style="max-height: 80px; overflow-y: auto;">
                                                    @foreach($existingNotes[$student->id_etudiant] as $note)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle d-flex align-items-center">
                                                            {{ $note->evaluation->matiere ? __('app.' . $note->evaluation->matiere->code_matiere) : $note->evaluation->matiere_name }}: {{ $note->note }}/{{ $note->evaluation->note_max }}
                                                            <button type="button" class="btn-close btn-close-sm ms-1" style="font-size: 0.6em;" 
                                                                    onclick="editNote({{ $note->id_note }}, {{ $student->id_etudiant }}, '{{ $student->prenom }} {{ $student->nom }}', {{ $note->id_evaluation }}, {{ $note->note }})" 
                                                                    title="{{ __('app.modifier') }}"></button>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ __('app.aucun_etudiant_trouve') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Grade Entry Form -->
                <div class="col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">
                                {{ __('app.formulaire_saisie_notes') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="gradeForm" method="POST" action="{{ route('enseignant.notes.store') }}">
                                @csrf
                                <input type="hidden" name="from_saisir_notes" value="1">
                                <input type="hidden" name="from_saisir_notes" value="1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="student_name" class="form-label fw-bold text-muted">
                                                {{ __('app.etudiant_selectionne') }}
                                            </label>
                                            <input type="text" class="form-control form-control-lg" id="student_name" readonly 
                                                   placeholder="{{ __('app.selectionnez_un_etudiant') }}">
                                            <input type="hidden" id="student_id" name="id_etudiant">
                                            <input type="hidden" id="student_matricule" name="student_matricule">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="evaluation_id" class="form-label fw-bold text-muted">
                                                {{ __('app.evaluation') }}
                                            </label>
                                            <select class="form-control form-control-lg" id="evaluation_id" name="id_evaluation" required onchange="updateEvaluationFields()" style="max-height: 200px; overflow-y: auto;">
                                                <option value="">{{ __('app.choisir_evaluation') }}</option>
                                                @foreach($evaluations as $evaluation)
                                                    <option value="{{ $evaluation->id_evaluation }}" 
                                                            data-matiere="{{ $evaluation->matiere ? $evaluation->matiere->nom_matiere : $evaluation->matiere_name }}" 
                                                            data-type="{{ $evaluation->type }}"
                                                            data-note-max="{{ $evaluation->note_max }}">
                                                        {{ $evaluation->matiere ? __('app.' . $evaluation->matiere->code_matiere) : $evaluation->matiere_name }} - {{ __('app.' . $evaluation->type) }} ({{ $evaluation->note_max }} pts)
                                                        @if($evaluation->date)
                                                            ({{ \Carbon\Carbon::parse($evaluation->date)->format('d/m/Y') }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                {{ __('app.evaluations_terminees_seulement') }}
                                            </small>
                                            @if($evaluations->isEmpty())
                                                <div class="alert alert-warning mt-2 mb-0">
                                                    {{ __('app.aucune_evaluation_terminee') }}
                                                </div>
                                            @endif
                                            <input type="hidden" id="matiere" name="matiere">
                                            <input type="hidden" id="type" name="type">
                                            <input type="hidden" name="id_classe" value="{{ $selectedClass->id_classe }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="note_obtenue" class="form-label fw-bold text-muted">
                                                {{ __('app.note_obtenue') }}
                                            </label>
                                            <input type="number" class="form-control form-control-lg text-center" id="note_obtenue" 
                                                   name="note" step="0.01" min="0" required>
                                            <input type="hidden" id="note_final" name="note">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="note_totale" class="form-label fw-bold text-muted">
                                                {{ __('app.note_totale') }}
                                            </label>
                                            <input type="number" class="form-control form-control-lg text-center" id="note_totale" 
                                                   name="note_totale" step="0.01" min="0" value="20" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-muted">
                                                {{ __('app.pourcentage') }}
                                            </label>
                                            <div class="form-control form-control-lg text-center bg-light" id="percentage">--</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="commentaire" class="form-label fw-bold text-muted">
                                        {{ __('app.commentaire') }} 
                                        <small class="text-muted">({{ __('app.optionnel') }})</small>
                                    </label>
                                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3" 
                                              placeholder="{{ __('app.commentaire_placeholder') }}"></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        {{ __('app.reinitialiser') }}
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg" disabled id="submitBtn">
                                        {{ __('app.enregistrer_note') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Notes for Selected Student -->
                    <div class="card shadow" id="recentNotesCard" style="display: none;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">
                                {{ __('app.notes_recentes') }}
                            </h6>
                        </div>
                        <div class="card-body" id="recentNotesContent">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-3x mb-3 text-muted"></i>
                <h4>{{ __('app.selectionner_classe') }}</h4>
                <p class="mb-0">{{ __('app.please_select_class') }}</p>
            </div>
        @endif
    @else
        <div class="alert alert-warning text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-muted"></i>
            <h4>{{ __('app.aucune_classe_assignee') }}</h4>
            <p class="mb-0">{{ __('app.contactez_administrateur') }}</p>
        </div>
    @endif
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}
.student-item:hover {
    background-color: #f8f9fa;
}
</style>

<script>
let selectedStudentId = null;
let selectedStudentName = null;

function selectStudent(studentId, studentName, matricule) {
    selectedStudentId = studentId;
    selectedStudentName = studentName;
    
    document.getElementById('student_id').value = studentId;
    document.getElementById('student_name').value = studentName;
    document.getElementById('student_matricule').value = matricule;
    document.getElementById('submitBtn').disabled = false;
    
    // Load recent notes for this student
    loadRecentNotes(studentId);
    
    // Highlight selected student
    document.querySelectorAll('.student-item').forEach(item => {
        item.classList.remove('bg-light', 'border-primary');
    });
    
    event.target.closest('.student-item').classList.add('bg-light', 'border-primary');
}

function resetForm() {
    document.getElementById('gradeForm').reset();
    document.getElementById('student_name').value = '';
    document.getElementById('student_id').value = '';
    document.getElementById('student_matricule').value = '';
    document.getElementById('note_final').value = '';
    document.getElementById('matiere').value = '';
    document.getElementById('type').value = '';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('percentage').textContent = '--';
    document.getElementById('recentNotesCard').style.display = 'none';
    
    // Remove highlight from students
    document.querySelectorAll('.student-item').forEach(item => {
        item.classList.remove('bg-light', 'border-primary');
    });
    
    selectedStudentId = null;
    selectedStudentName = null;
}

function updateEvaluationFields() {
    const select = document.getElementById('evaluation_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        document.getElementById('matiere').value = selectedOption.getAttribute('data-matiere') || '';
        document.getElementById('type').value = selectedOption.getAttribute('data-type') || '';
        const noteMax = selectedOption.getAttribute('data-note-max') || '20';
        document.getElementById('note_totale').value = noteMax;
        
        // Update the max attribute for the note input
        document.getElementById('note_obtenue').setAttribute('max', noteMax);
        
        // Recalculate percentage if there's already a note
        calculatePercentage();
    } else {
        document.getElementById('matiere').value = '';
        document.getElementById('type').value = '';
        document.getElementById('note_totale').value = '';
        document.getElementById('note_obtenue').removeAttribute('max');
    }
}

function calculatePercentage() {
    const noteObtenue = parseFloat(document.getElementById('note_obtenue').value) || 0;
    const noteTotale = parseFloat(document.getElementById('note_totale').value) || 0;
    
    if (noteTotale > 0) {
        const percentage = (noteObtenue / noteTotale * 100).toFixed(1);
        document.getElementById('percentage').textContent = percentage + '%';
        
        // Calculate final grade out of 20 (standard French grading system)
        const finalGrade = (noteObtenue / noteTotale * 20).toFixed(2);
        document.getElementById('note_final').value = finalGrade;
        
        // Color coding
        const percentageEl = document.getElementById('percentage');
        percentageEl.classList.remove('text-danger', 'text-warning', 'text-success');
        
        if (percentage >= 70) percentageEl.classList.add('text-success');
        else if (percentage >= 50) percentageEl.classList.add('text-warning');
        else percentageEl.classList.add('text-danger');
    } else {
        document.getElementById('percentage').textContent = '--';
        document.getElementById('note_final').value = '';
    }
}

function loadRecentNotes(studentId) {
    // Show the recent notes card
    document.getElementById('recentNotesCard').style.display = 'block';
    document.getElementById('recentNotesContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> {{ __("app.loading") }}...</div>';
    
    // You can implement AJAX call here to load recent notes
    // For now, we'll show a placeholder
    setTimeout(() => {
        document.getElementById('recentNotesContent').innerHTML = `
            <div class="text-muted text-center">
                <i class="fas fa-chart-line"></i> {{ __("app.notes_precedentes_etudiant") }}
                <br><small>{{ __("app.fonctionnalite_bientot_disponible") }}</small>
            </div>
        `;
    }, 1000);
}

// Search functionality
document.getElementById('searchStudent').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const students = document.querySelectorAll('.student-item');
    
    students.forEach(student => {
        const studentName = student.getAttribute('data-student');
        if (studentName.includes(searchTerm)) {
            student.style.display = 'block';
        } else {
            student.style.display = 'none';
        }
    });
});

// Auto-calculate percentage
document.getElementById('note_obtenue').addEventListener('input', calculatePercentage);
document.getElementById('note_totale').addEventListener('input', calculatePercentage);

// Form validation
document.getElementById('gradeForm').addEventListener('submit', function(e) {
    const noteObtenue = parseFloat(document.getElementById('note_obtenue').value);
    const noteTotale = parseFloat(document.getElementById('note_totale').value);
    
    if (noteObtenue > noteTotale) {
        e.preventDefault();
        alert('{{ __("app.grade_exceeds_total") }}');
        return false;
    }
    
    if (!selectedStudentId) {
        e.preventDefault();
        alert('{{ __("app.please_select_student") }}');
        return false;
    }
});

// Edit existing note function
function editNote(noteId, studentId, studentName, evaluationId, currentNote) {
    // Select the student
    selectStudent(studentId, studentName, '');
    
    // Set the evaluation
    document.getElementById('evaluation_id').value = evaluationId;
    updateEvaluationFields();
    
    // Set the current note value
    document.getElementById('note_obtenue').value = currentNote;
    
    // Update form to edit mode
    const form = document.getElementById('gradeForm');
    form.action = "{{ route('enseignant.notes.update', ':noteId') }}".replace(':noteId', noteId);
    
    // Add method field for PUT request
    let methodField = document.getElementById('_method');
    if (!methodField) {
        methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.id = '_method';
        form.appendChild(methodField);
    }
    methodField.value = 'PUT';
    
    // Update submit button text
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>{{ __("app.modifier_note") }}';
    submitBtn.classList.remove('btn-success');
    submitBtn.classList.add('btn-warning');
    
    // Add cancel button if not exists
    let cancelBtn = document.getElementById('cancelBtn');
    if (!cancelBtn) {
        cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.id = 'cancelBtn';
        cancelBtn.className = 'btn btn-secondary me-2';
        cancelBtn.innerHTML = '<i class="fas fa-times me-1"></i>{{ __("app.annuler") }}';
        cancelBtn.onclick = cancelEdit;
        submitBtn.parentNode.insertBefore(cancelBtn, submitBtn);
    }
    
    // Calculate percentage
    calculatePercentage();
    
    // Scroll to form
    document.getElementById('gradeForm').scrollIntoView({ behavior: 'smooth' });
}

// Cancel edit function
function cancelEdit() {
    // Reset form action
    document.getElementById('gradeForm').action = "{{ route('enseignant.notes.store') }}";
    
    // Remove method field
    const methodField = document.getElementById('_method');
    if (methodField) {
        methodField.remove();
    }
    
    // Reset submit button
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>{{ __("app.enregistrer_note") }}';
    submitBtn.classList.remove('btn-warning');
    submitBtn.classList.add('btn-success');
    
    // Remove cancel button
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.remove();
    }
    
    // Reset form
    resetForm();
}
</script>
@endsection
