@extends('layouts.dashboard')

@section('title', __('app.mon_emploi_du_temps'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('app.mon_enseignement') }}</li>
    <li class="breadcrumb-item active">{{ __('app.mon_emploi_du_temps') }}</li>
@endsection

@section('content')
<div class="container-fluid">
    

    <!-- Responsive Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div class="flex-grow-1 mb-2 mb-md-0">
            <h4 class="mb-1 fs-5 fs-md-4">{{ __('app.mon_emploi_du_temps') }}</h4>
            <p class="text-muted mb-0 small">{{ __('app.semaine_du') }} {{ date('d/m/Y', strtotime('monday this week')) }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="printTimetable()">
                <i class="fas fa-print"></i> <span class="d-none d-sm-inline">{{ __('app.imprimer') }}</span>
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="toggleView()">
                <i class="fas fa-list"></i> <span id="viewToggleText" class="d-none d-sm-inline">{{ __('app.vue_liste') }}</span>
            </button>
            <!-- Mobile-specific quick toggle -->
            <button type="button" class="btn btn-secondary btn-sm d-md-none" onclick="toggleMobileView()" id="mobileToggle">
                <i class="fas fa-mobile-alt"></i> {{ __('app.mobile') }}
            </button>
        </div>
    </div>

    @if($courses->count() > 0)
        <!-- Responsive Quick Stats -->
        <div class="row mb-4 g-2 g-md-3">
            <div class="col-6 col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body py-2 py-md-3">
                        <h5 class="text-primary mb-1 fs-6 fs-md-5">{{ $courses->count() }}</h5>
                        <small class="text-muted d-block">{{ __('app.total_cours') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body py-2 py-md-3">
                        <h5 class="text-info mb-1 fs-6 fs-md-5">{{ $courses->groupBy('matiere')->count() }}</h5>
                        <small class="text-muted d-block">{{ __('app.matieres') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body py-2 py-md-3">
                        <h5 class="text-success mb-1 fs-6 fs-md-5">{{ $courses->groupBy('id_classe')->count() }}</h5>
                        <small class="text-muted d-block">{{ __('app.classes') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body py-2 py-md-3">
                        <h5 class="text-warning mb-1 fs-6 fs-md-5">{{ $courses->count() * 2 }}h</h5>
                        <small class="text-muted d-block">{{ __('app.heures_semaine') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Timetable -->
        <div class="card" id="weekView">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h6 class="mb-1 mb-md-0">
                    <i class="fas fa-calendar-week text-primary"></i> 
                    <span class="d-none d-sm-inline">{{ __('app.emploi_du_temps_hebdomadaire') }}</span>
                    <span class="d-sm-none">{{ __('app.planning') }}</span>
                </h6>
                <small class="text-muted">{{ $teacher->name }}</small>
            </div>
            
            <!-- Mobile/Tablet scroll hint -->
            <div class="alert alert-info alert-sm d-md-none mb-0" style="border-radius: 0; font-size: 0.8rem;">
                <i class="fas fa-info-circle"></i> 
                {{ __('app.faites_defiler_horizontalement') }}
                Faites défiler horizontalement pour voir tous les jours
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 table-sm table-md-normal">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center time-header">
                                    <i class="fas fa-clock text-primary d-none d-sm-inline"></i>
                                    <span class="d-none d-sm-block"><small>{{ __('app.horaires') }}</small></span>
                                    <span class="d-sm-none">H</span>
                                </th>
                                <th class="text-center day-header">
                                    <strong class="d-none d-sm-inline">{{ __('app.lundi') }}</strong>
                                    <strong class="d-sm-none">Lun</strong><br>
                                    <small class="text-muted">{{ date('d/m', strtotime('monday this week')) }}</small>
                                </th>
                                <th class="text-center day-header">
                                    <strong class="d-none d-sm-inline">{{ __('app.mardi') }}</strong>
                                    <strong class="d-sm-none">Mar</strong><br>
                                    <small class="text-muted">{{ date('d/m', strtotime('tuesday this week')) }}</small>
                                </th>
                                <th class="text-center day-header">
                                    <strong class="d-none d-sm-inline">{{ __('app.mercredi') }}</strong>
                                    <strong class="d-sm-none">Mer</strong><br>
                                    <small class="text-muted">{{ date('d/m', strtotime('wednesday this week')) }}</small>
                                </th>
                                <th class="text-center day-header">
                                    <strong class="d-none d-sm-inline">{{ __('app.jeudi') }}</strong>
                                    <strong class="d-sm-none">Jeu</strong><br>
                                    <small class="text-muted">{{ date('d/m', strtotime('thursday this week')) }}</small>
                                </th>
                                <th class="text-center day-header">
                                    <strong class="d-none d-sm-inline">{{ __('app.vendredi') }}</strong>
                                    <strong class="d-sm-none">Ven</strong><br>
                                    <small class="text-muted">{{ date('d/m', strtotime('friday this week')) }}</small>
                                </th>
                                <th class="text-center day-header">
                                    <strong class="d-none d-sm-inline">{{ __('app.samedi') }}</strong>
                                    <strong class="d-sm-none">Sam</strong><br>
                                    <small class="text-muted">{{ date('d/m', strtotime('saturday this week')) }}</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slot)
                                <tr>
                                    <td class="time-slot">
                                        <strong>{{ $slot['time'] }}</strong>
                                        @if($slot['period'] === 'pause')
                                            <br><small class="text-warning">{{ __('app.pause') }}</small>
                                        @endif
                                    </td>
                                    @foreach($days as $day)
                                        <td class="text-center schedule-cell" style="vertical-align: middle;">
                                            @if($slot['period'] === 'pause')
                                                <small class="text-muted">
                                                    <i class="fas fa-coffee d-none d-sm-inline"></i> 
                                                    <span class="d-none d-sm-inline">{{ __('app.pause') }}</span>
                                                    <span class="d-sm-none">P</span>
                                                </small>
                                            @elseif(isset($organizedCourses[$day][$slot['time']]) && $organizedCourses[$day][$slot['time']])
                                                @php $course = $organizedCourses[$day][$slot['time']]; @endphp
                                                <div class="course-cell">
                                                    <div class="matiere" title="{{ $course->matiere }}">{{ $course->matiere }}</div>
                                                    <div class="classe" title="{{ $course->classe->nom_classe ?? 'N/A' }}">{{ $course->classe->nom_classe ?? 'N/A' }}</div>
                                                </div>
                                            @else
                                                <div class="empty-cell">
                                                    <small class="text-muted">
                                                        <span class="d-none d-sm-inline">{{ __('app.libre') }}</span>
                                                        <span class="d-sm-none">-</span>
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Enhanced Print Footer (Only visible when printing) -->
        <div class="print-footer d-none" style="margin-top: 20px; border-top: 2px solid #000; padding: 12px 0;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <!-- Left section: Statistics -->
                <div style="text-align: left; flex: 1; font-size: 8pt; color: #333;">
                    <div style="font-weight: bold; margin-bottom: 3px;">{{ __('app.statistiques_hebdomaidaires') }}</div>
                    <div style="font-size: 7pt;">
                        <strong>{{ $courses->count() }} {{ __('app.cours') }}</strong> • 
                        <strong>{{ $courses->groupBy('matiere')->count() }} {{ __('app.matiere') }}</strong> • 
                        <strong>{{ $courses->groupBy('id_classe')->count() }} {{ __('app.classe') }}</strong> • 
                        <strong>{{ $courses->count() * 2 }} {{ __('app.heures_semaine') }}</strong>
                    </div>
                </div>
                
                <!-- Center section: Legend -->
                <div style="text-align: center; flex: 1; font-size: 7pt; color: #666;">
                    <div style="margin-bottom: 2px;"><strong>{{ __('app.legend') }}</strong></div>
                    <div style="display: inline-flex; gap: 15px;">
                        <span style="background-color: #e3f2fd; padding: 2px 4px; border: 1px solid #2196f3;">{{ __('app.cours_programme') }}</span>
                        <span style="background-color: #f5f5f5; padding: 2px 4px; border: 1px solid #ccc;">{{ __('app.temps_libre') }}</span>
                    </div>
                </div>
                
                <!-- Right section: Contact info -->
                <div style="text-align: right; flex: 1; font-size: 7pt; color: #666;">
                    <div style="font-weight: bold; margin-bottom: 2px;">{{ __('app.contact') }}</div>
                    <div>{{ $teacher->email ?? 'email@ecole.com' }}</div>
                    <div>Tél: {{ $teacher->telephone ?? '+222 XX XX XX XX' }}</div>
                </div>
            </div>
            
            <!-- Bottom signature line -->
            <div style="margin-top: 12px; text-align: center; font-size: 6pt; color: #999; border-top: 1px solid #ddd; padding-top: 6px;">
                {{ __('app.document_generated_on') }} {{ date('d/m/Y à H:i') }} • {{ config('app.name', 'Système de Gestion Scolaire') }} • Page 1/1
            </div>
        </div>

        <!-- Responsive List View -->
        <div class="card d-none" id="listView">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-list text-primary"></i> 
                    <span class="d-none d-sm-inline">{{ __('app.course_list') }}</span>
                    <span class="d-sm-none">{{ __('app.cours') }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2 g-md-3">
                    @foreach($courses->groupBy('matiere') as $matiere => $coursesGroup)
                        <div class="col-12 col-md-6 col-lg-4 mb-2 mb-md-3">
                            <div class="card border-start border-primary border-3 h-100">
                                <div class="card-body">
                                    <h6 class="text-primary mb-2 fs-6">{{ $matiere }}</h6>
                                    @foreach($coursesGroup as $course)
                                        <div class="mb-2">
                                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                                                <span class="fw-bold fs-7 fs-sm-6">{{ ucfirst($course->jour) }}</span>
                                                <span class="text-muted small">
                                                    {{ date('H:i', strtotime($course->date_debut)) }} - {{ date('H:i', strtotime($course->date_fin)) }}
                                                </span>
                                            </div>
                                            <small class="text-muted d-block">{{ $course->classe->nom_classe ?? 'N/A' }}</small>
                                        </div>
                                        @if(!$loop->last)<hr class="my-2">@endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @else
        <!-- No Courses -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('app.aucun_cours_assigne') }}</h5>
                <p class="text-muted">{{ __('app.vous_navez_aucun_cours_assigne_pour_le_moment') }}</p>
            </div>
        </div>
    @endif
</div>

<script>
function toggleView() {
    const weekView = document.getElementById('weekView');
    const listView = document.getElementById('listView');
    const toggleText = document.getElementById('viewToggleText');
    
    if (weekView.classList.contains('d-none')) {
        weekView.classList.remove('d-none');
        listView.classList.add('d-none');
        toggleText.textContent = 'Vue Liste';
        localStorage.setItem('timetableView', 'week');
    } else {
        weekView.classList.add('d-none');
        listView.classList.remove('d-none');
        toggleText.textContent = 'Vue Semaine';
        localStorage.setItem('timetableView', 'list');
    }
}

// Mobile-specific view toggle
function toggleMobileView() {
    const body = document.body;
    const mobileToggle = document.getElementById('mobileToggle');
    
    if (body.classList.contains('mobile-view-active')) {
        body.classList.remove('mobile-view-active');
        mobileToggle.innerHTML = '<i class="fas fa-mobile-alt"></i> Mobile';
    } else {
        body.classList.add('mobile-view-active');
        mobileToggle.innerHTML = '<i class="fas fa-desktop"></i> Bureau';
    }
}

// Remember user's view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('timetableView');
    if (savedView === 'list') {
        toggleView();
    }
    
    // Check if mobile device and auto-switch to list view
    if (window.innerWidth < 768 && !savedView) {
        const weekView = document.getElementById('weekView');
        const listView = document.getElementById('listView');
        const toggleText = document.getElementById('viewToggleText');
        
        weekView.classList.add('d-none');
        listView.classList.remove('d-none');
        toggleText.textContent = 'Vue Semaine';
    }
});

// Enhanced print function with better PDF generation
function printTimetable() {
    // Show only timetable view for printing
    const weekView = document.getElementById('weekView');
    const listView = document.getElementById('listView');
    
    if (!weekView.classList.contains('d-none')) {
        // Already showing week view, just print
        window.print();
    } else {
        // Switch to week view, print, then switch back
        const wasListView = !listView.classList.contains('d-none');
        
        // Show week view
        weekView.classList.remove('d-none');
        listView.classList.add('d-none');
        
        // Print after a short delay to ensure layout is ready
        setTimeout(() => {
            window.print();
            
            // Switch back to list view if it was showing
            if (wasListView) {
                setTimeout(() => {
                    weekView.classList.add('d-none');
                    listView.classList.remove('d-none');
                }, 100);
            }
        }, 100);
    }
}

// Update the print button to use the enhanced function
document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.querySelector('button[onclick="window.print()"]');
    if (printBtn) {
        printBtn.setAttribute('onclick', 'printTimetable()');
    }
    
    // Add print event listener for better control
    window.addEventListener('beforeprint', function() {
        // Ensure we're showing the timetable view
        const weekView = document.getElementById('weekView');
        const listView = document.getElementById('listView');
        
        weekView.classList.remove('d-none');
        listView.classList.add('d-none');
    });
});
</script>

<style>
/* Responsive Screen Styles */
.course-cell {
    background-color: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 4px;
    padding: 8px;
    text-align: center;
    font-size: 0.85rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

.course-cell .matiere {
    font-weight: bold;
    color: #1976d2;
    margin-bottom: 2px;
    line-height: 1.2;
}

.course-cell .classe {
    color: #666;
    font-size: 0.8rem;
    margin-top: 4px;
    line-height: 1.1;
}

.empty-cell {
    background-color: #f5f5f5;
    text-align: center;
    color: #ccc;
    font-style: italic;
}

.time-slot {
    font-weight: bold;
    text-align: center;
    vertical-align: middle;
    background-color: #f8f9fa;
    white-space: nowrap;
}

/* Mobile First Responsive Design */
@media (max-width: 576px) {
    /* Mobile phones */
    .container-fluid {
        padding: 0.5rem !important;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div:last-child {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .row.mb-4 .col-md-3 {
        margin-bottom: 0.75rem;
    }
    
    .card-body.py-3 {
        padding: 0.75rem !important;
    }
    
    /* Hide table and show mobile-friendly list view */
    #weekView .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        min-width: 800px;
        font-size: 0.7rem;
    }
    
    .table th,
    .table td {
        padding: 0.25rem;
        vertical-align: middle;
    }
    
    .time-slot {
        font-size: 0.65rem;
        padding: 0.25rem;
        width: 60px;
    }
    
    .course-cell {
        padding: 4px;
        font-size: 0.65rem;
    }
    
    .course-cell .matiere {
        font-size: 0.6rem;
        margin-bottom: 1px;
    }
    
    .course-cell .classe {
        font-size: 0.55rem;
        margin-top: 1px;
    }
    
    .card-header h6 {
        font-size: 0.9rem;
    }
    
    .card-header small {
        font-size: 0.7rem;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    /* Tablets */
    .container-fluid {
        padding: 0.75rem !important;
    }
    
    .d-flex.justify-content-between {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .row.mb-4 .col-md-3 {
        flex: 0 0 50%;
        max-width: 50%;
        margin-bottom: 1rem;
    }
    
    .table {
        font-size: 0.8rem;
    }
    
    .table th,
    .table td {
        padding: 0.375rem;
    }
    
    .time-slot {
        font-size: 0.75rem;
        width: 80px;
    }
    
    .course-cell {
        padding: 6px;
        font-size: 0.75rem;
    }
    
    .course-cell .matiere {
        font-size: 0.7rem;
    }
    
    .course-cell .classe {
        font-size: 0.65rem;
    }
}

@media (min-width: 769px) and (max-width: 992px) {
    /* Small desktops / Large tablets */
    .container-fluid {
        padding: 1rem !important;
    }
    
    .row.mb-4 .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .time-slot {
        width: 90px;
    }
    
    .course-cell {
        padding: 7px;
        font-size: 0.8rem;
    }
}

@media (min-width: 993px) {
    /* Large desktops */
    .container-fluid {
        padding: 1.25rem !important;
    }
    
    .table td {
        height: 80px;
    }
    
    .time-slot {
        width: 100px;
    }
}

/* Enhanced Mobile View Toggle */
@media (max-width: 768px) {
    .mobile-schedule-toggle {
        display: block !important;
        margin-bottom: 1rem;
    }
    
    .mobile-view-active #weekView {
        display: none !important;
    }
    
    .mobile-view-active #listView {
        display: block !important;
    }
}

/* List View Responsive Enhancements */
@media (max-width: 576px) {
    #listView .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .card.border-start {
        margin-bottom: 0.75rem;
    }
    
    .card-body h6 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .fw-bold {
        font-size: 0.9rem;
    }
    
    .text-muted {
        font-size: 0.8rem;
    }
}

/* Horizontal scroll indicator for mobile */
@media (max-width: 768px) {
    .table-responsive::after {
        content: "← Faites défiler horizontalement →";
        display: block;
        text-align: center;
        font-size: 0.7rem;
        color: #666;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    .table-responsive:hover::after {
        display: none;
    }
}

/* Professional Print Styles */
@media print {
    @page {
        size: A4 landscape;
        margin: 0.2in;
    }
    
    body {
        font-family: 'Arial', sans-serif !important;
        font-size: 8pt !important;
        line-height: 1.1 !important;
        color: #000 !important;
        background: white !important;
    }
    
    /* Print Header Styles */
    .print-header-dynamic {
        display: block !important;
        text-align: center;
        font-weight: bold;
        font-size: 12pt;
        line-height: 1.3;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #000;
        color: #000 !important;
        page-break-after: avoid;
    }
    
    /* Hide screen elements completely */
    .btn, .navbar, .sidebar, .breadcrumb, 
    .card-header, .no-print, .d-print-none,
    .container-fluid > .d-flex,
    .d-flex.justify-content-between.align-items-center.mb-4,
    .d-flex.justify-content-between.align-items-center.mb-3,
    .row.mb-4,
    h1, h2, h3, h4, h5, h6 {
        display: none !important;
    }
    
    /* Show print elements */
    .print-header-dynamic {
        display: block !important;
    }
    
    .d-none.print-header-dynamic {
        display: block !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    .d-none.print-only {
        display: block !important;
    }
    
    /* Container styling */
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
    
    /* Card styling */
    .card {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        page-break-inside: avoid;
    }
    
    .card-body {
        padding: 0 !important;
    }
    
    /* Table styling - Compressed for one page */
    .table {
        width: 95% !important;
        border-collapse: collapse !important;
        margin: 0 auto !important;
        font-size: 8pt !important;
        page-break-inside: avoid;
    }
    
    .table th {
        background-color: #e9ecef !important;
        border: 1px solid #000 !important;
        padding: 2px 1px !important;
        text-align: center !important;
        font-weight: bold !important;
        font-size: 6pt !important;
        height: 25px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .table td {
        border: 1px solid #000 !important;
        padding: 1px !important;
        vertical-align: middle !important;
        height: 30px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Time column */
    .time-slot {
        background-color: #f8f9fa !important;
        font-weight: bold !important;
        text-align: center !important;
        vertical-align: middle !important;
        width: 60px !important;
        font-size: 6pt !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Course cells */
    .course-cell {
        background-color: #e3f2fd !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 4px !important;
        text-align: center !important;
        font-size: 8pt !important;
        height: 100% !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .course-cell .matiere {
        font-weight: bold !important;
        color: #000 !important;
        margin-bottom: 1px !important;
        font-size: 6pt !important;
        text-transform: uppercase;
        line-height: 1.0;
        letter-spacing: 0.2px;
    }
    
    .course-cell .classe {
        color: #333 !important;
        font-size: 5pt !important;
        font-weight: normal !important;
        line-height: 1.0;
        margin-top: 1px !important;
    }
    
    /* Empty cells */
    .empty-cell {
        background-color: #f8f9fa !important;
        text-align: center !important;
        color: #ccc !important;
        font-style: italic !important;
        font-size: 8pt !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Stats cards - hide for print */
    .row.mb-4 {
        display: none !important;
    }
    
    /* Enhanced Print footer */
    .print-footer {
        display: block !important;
        margin-top: 15px !important;
        border-top: 2px solid #000 !important;
        padding: 8px 0 !important;
        page-break-inside: avoid !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-size: 7pt !important;
        color: #333 !important;
    }
    
    .print-footer div {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Force exact colors */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<script>
// Enhanced Print Functionality with Dynamic Header
function printTimetable() {
    // Create dynamic print header
    let printHeader = document.getElementById('dynamic-print-header');
    if (!printHeader) {
        printHeader = document.createElement('div');
        printHeader.id = 'dynamic-print-header';
        printHeader.className = 'print-header-dynamic d-none';
        
        // Get current date info
        const currentDate = new Date();
        const academicYear = '{{ date("Y") }}-{{ date("Y") + 1 }}';
        const semester = currentDate.getMonth() >= 8 ? '1er Semestre' : '2ème Semestre';
        
        printHeader.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; border: 2px solid #000; background-color: #f8f9fa;">
                <div style="text-align: left; flex: 1;">
                    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 2px;">ÉTABLISSEMENT SCOLAIRE</div>
                    <div style="font-size: 8pt; color: #666;">{{ config('app.name', 'École') }}</div>
                    <div style="font-size: 7pt; color: #666;">Année: ${academicYear}</div>
                </div>
                
                <div style="text-align: center; flex: 2;">
                    <div style="font-size: 16pt; font-weight: bold; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1.5px; color: #1976d2;">
                        EMPLOI DU TEMPS
                    </div>
                    <div style="font-size: 11pt; font-weight: 600; margin-bottom: 4px; color: #333;">
                        {{ $teacher->name ?? 'Enseignant' }}
                    </div>
                    <div style="font-size: 8pt; color: #666; font-weight: 500;">
                        ${semester} • Semaine du {{ date('d/m/Y', strtotime('monday this week')) }} au {{ date('d/m/Y', strtotime('friday this week')) }}
                    </div>
                </div>
                
                <div style="text-align: right; flex: 1;">
                    <div style="font-size: 8pt; color: #666;">Édité le: ${new Date().toLocaleDateString('fr-FR')}</div>
                    <div style="font-size: 7pt; color: #666;">à ${new Date().toLocaleTimeString('fr-FR')}</div>
                    <div style="font-size: 7pt; color: #666; margin-top: 3px; font-weight: 500;">{{ $courses->count() }} cours planifiés</div>
                </div>
            </div>
        `;
        
        // Insert header at the beginning of container
        const container = document.querySelector('.container-fluid');
        container.insertBefore(printHeader, container.firstChild);
    }
    
    // Hide browser headers/footers and optimize print
    const originalTitle = document.title;
    document.title = 'Emploi_du_Temps_' + new Date().toISOString().slice(0,10);
    
    // Trigger print dialog
    window.print();
    
    // Restore original title after print
    setTimeout(() => {
        document.title = originalTitle;
    }, 1000);
}

// Override default print button behavior
document.addEventListener('DOMContentLoaded', function() {
    // Find and override any print buttons
    const printButtons = document.querySelectorAll('[onclick*="print"], .btn-print');
    printButtons.forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            printTimetable();
        };
    });
    
    // Add keyboard shortcut for print
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            printTimetable();
        }
    });
});
</script>

@endsection
