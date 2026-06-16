<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relevé de Notes - {{ $etudiant->nom }} {{ $etudiant->prenom }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">
    
    @include('components.public-styles')
    
    <style>
        body {
            background: #f8f9fa;
            padding: 0;
        }
        
        .main-content {
            padding: 2rem 1rem;
        }
        
        .transcript-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .transcript-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .transcript-header {
            background: #2563eb;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .transcript-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0 0 0.5rem;
        }
        
        .transcript-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        .transcript-body {
            padding: 2rem;
        }
        
        .student-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .student-info table {
            width: 100%;
        }
        
        .student-info td {
            padding: 0.5rem;
            border: none;
        }
        
        .student-info strong {
            color: #495057;
        }
        
        .filters {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .matiere-section {
            margin-bottom: 2rem;
        }
        
        .notes-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 8px 8px;
        }
        
        .matiere-header {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
            border-bottom: 2px solid #2563eb;
        }
        
        .matiere-title {
            font-weight: 600;
            color: #212529;
            margin: 0;
        }
        
        .matiere-average {
            color: #2563eb;
            font-weight: 600;
        }
        
        .notes-table {
            margin: 0;
        }
        
        .notes-table th {
            background: #f8f9fa;
            font-weight: 500;
            padding: 0.75rem;
        }
        
        .notes-table td {
            padding: 0.75rem;
        }
        
        .statistics {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2563eb;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .back-btn {
            margin-bottom: 1.5rem;
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }
        
        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        
        .print-btn {
            float: right;
        }
        
        /* RTL Support */
        html[dir="rtl"] .back-btn .btn {
            display: inline-flex;
            flex-direction: row-reverse;
        }
        
        html[dir="rtl"] .back-btn .btn i {
            margin-right: 0;
            margin-left: 0.5rem;
        }
        
        html[dir="rtl"] .print-btn {
            float: left;
        }
        
        html[dir="rtl"] .print-btn .btn {
            display: inline-flex;
            flex-direction: row-reverse;
        }
        
        html[dir="rtl"] .print-btn .btn i {
            margin-right: 0;
            margin-left: 0.5rem;
        }
        
        html[dir="rtl"] .matiere-header {
            text-align: right;
        }
        
        html[dir="rtl"] .student-info,
        html[dir="rtl"] .statistics,
        html[dir="rtl"] .filters {
            text-align: right;
        }
        
        /* Responsive Design - Mobile First Approach */
        
        /* Small devices (phones, less than 576px) */
        @media (max-width: 576px) {
            .main-content {
                padding: 1rem 0.5rem;
            }
            
            .transcript-container {
                max-width: 100%;
            }
            
            .transcript-header {
                padding: 1.5rem 1rem;
            }
            
            .transcript-header h1 {
                font-size: 1.25rem;
            }
            
            .transcript-header p {
                font-size: 0.875rem;
            }
            
            .transcript-body {
                padding: 1rem;
            }
            
            .student-info,
            .filters,
            .statistics {
                padding: 1rem;
            }
            
            /* Stack student info vertically */
            .student-info table {
                display: block;
            }
            
            .student-info tr {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.25rem;
                margin-bottom: 1rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .student-info tr:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            
            .student-info td {
                display: block;
                padding: 0.25rem 0;
            }
            
            .student-info td:first-child {
                font-weight: 600;
                color: #374151;
            }
            
            /* Back and print buttons */
            .back-btn {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .back-btn .btn {
                width: 100%;
                justify-content: center;
                font-size: 0.875rem;
            }
            
            .print-btn {
                float: none !important;
            }
            
            html[dir="rtl"] .print-btn {
                float: none !important;
            }
            
            /* Filters */
            .filters .row {
                gap: 1rem;
            }
            
            .filters .col-md-6 {
                margin-bottom: 1rem;
            }
            
            .filters .col-md-6:last-child {
                margin-bottom: 0;
            }
            
            /* Matiere header */
            .matiere-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            
            .matiere-title {
                font-size: 1rem;
            }
            
            .matiere-average {
                font-size: 0.875rem;
            }
            
            /* Notes table - make scrollable */
            .notes-table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .notes-table {
                font-size: 0.875rem;
                min-width: 500px;
            }
            
            .notes-table th,
            .notes-table td {
                padding: 0.5rem 0.375rem;
                font-size: 0.8125rem;
            }
            
            /* Statistics grid */
            .stat-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .stat-value {
                font-size: 1.75rem;
            }
            
            .stat-label {
                font-size: 0.8125rem;
            }
            
            /* Form controls */
            .form-label {
                font-size: 0.875rem;
            }
            
            .form-select {
                font-size: 0.875rem;
            }
        }
        
        /* Medium devices (tablets, 577px to 768px) */
        @media (min-width: 577px) and (max-width: 768px) {
            .main-content {
                padding: 1.5rem 1rem;
            }
            
            .transcript-header h1 {
                font-size: 1.5rem;
            }
            
            .transcript-body {
                padding: 1.5rem;
            }
            
            .student-info table {
                font-size: 0.9375rem;
            }
            
            .back-btn .btn {
                font-size: 0.9375rem;
            }
            
            .stat-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .stat-value {
                font-size: 1.875rem;
            }
            
            .notes-table {
                font-size: 0.9375rem;
            }
        }
        
        /* Large devices (desktops, 769px to 992px) */
        @media (min-width: 769px) and (max-width: 992px) {
            .transcript-container {
                max-width: 800px;
            }
            
            .stat-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        /* Extra large devices (large desktops, 1200px and up) */
        @media (min-width: 1200px) {
            .transcript-container {
                max-width: 1000px;
            }
            
            .transcript-header {
                padding: 2.5rem;
            }
            
            .transcript-header h1 {
                font-size: 2rem;
            }
            
            .transcript-body {
                padding: 2.5rem;
            }
        }
        
        /* Landscape orientation fixes for mobile */
        @media (max-height: 600px) and (orientation: landscape) {
            .main-content {
                padding: 1rem;
            }
            
            .transcript-header {
                padding: 1rem;
            }
            
            .transcript-header h1 {
                font-size: 1.25rem;
                margin-bottom: 0.25rem;
            }
            
            .transcript-body {
                padding: 1rem;
            }
            
            .student-info,
            .filters,
            .statistics {
                padding: 1rem;
                margin-bottom: 1rem;
            }
        }
        
        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .form-select,
            .btn {
                min-height: 44px;
            }
            
            .notes-table th,
            .notes-table td {
                min-height: 44px;
            }
        }
        
        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .transcript-card {
                box-shadow: none;
            }
            
            .main-content {
                padding: 0;
            }
            
            .transcript-header {
                background: #2563eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .matiere-header {
                border-bottom: 2px solid #2563eb !important;
            }
            
            .notes-table {
                page-break-inside: avoid;
            }
            
            .matiere-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @include('components.public-nav')
    
    <div class="main-content">
        <div class="transcript-container">
            <!-- Actions -->
            <div class="no-print back-btn">
                <a href="{{ route('accueil') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    {{ __('app.retour_a_la_recherche') }}
                </a>
                <!-- <button onclick="window.print()" class="btn btn-primary print-btn">
                    <i class="fas fa-print me-2"></i>
                    {{ __('app.imprimer') }}
                </button> -->
            </div>
        
        <div class="transcript-card">
            <!-- Header -->
            <div class="transcript-header">
                <h1>{{ __('app.releve_de_notes') }}</h1>
                @if($trimestreInfo)
                    <p>{{ $trimestreInfo['name'] }} - {{ __('app.annee_scolaire') }} {{ $academicYear }}</p>
                @else
                    <p>{{ __('app.annee_scolaire') }} {{ $academicYear }}</p>
                @endif
            </div>
            
            <div class="transcript-body">
                <!-- Student Information -->
                <div class="student-info">
                    <table>
                        <tr>
                            <td><strong>{{ __('app.nom') }}:</strong></td>
                            <td>{{ $etudiant->nom }} {{ $etudiant->prenom }}</td>
                            <td><strong>{{ __('app.matricule') }}:</strong></td>
                            <td>{{ $etudiant->matricule }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('app.classe') }}:</strong></td>
                            <td>{{ $etudiant->classe->nom_classe ?? 'N/A' }}</td>
                            <td><strong>{{ __('app.date_naissance') }}:</strong></td>
                            <td>{{ $etudiant->date_naissance?->format('d/m/Y') ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Filters -->
                <div class="filters no-print">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.annee_scolaire') }}</label>
                            <select class="form-select" id="yearSelect" onchange="changeYear()">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == $academicYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('app.trimestre') }}</label>
                            <select class="form-select" id="trimestreSelect" onchange="changeTrimestre()">
                                <option value="" {{ !$trimestre ? 'selected' : '' }}>{{ __('app.tous_les_trimestres') }}</option>
                                <option value="1" {{ $trimestre == '1' ? 'selected' : '' }}>{{ __('app.premier_trimestre') }}</option>
                                <option value="2" {{ $trimestre == '2' ? 'selected' : '' }}>{{ __('app.deuxieme_trimestre') }}</option>
                                <option value="3" {{ $trimestre == '3' ? 'selected' : '' }}>{{ __('app.troisieme_trimestre') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Notes by Matiere -->
                @if($notesByMatiere->count() > 0)
                    @foreach($notesByMatiere as $matiere => $matiereNotes)
                        <div class="matiere-section">
                            <div class="matiere-header d-flex justify-content-between align-items-center">
                                <h5 class="matiere-title">{{ __('app.'.$matiere) }}</h5>
                                @php
                                    $totalPoints = 0;
                                    $totalMaxPoints = 0;
                                    foreach($matiereNotes as $note) {
                                        $totalPoints += $note->note;
                                        $totalMaxPoints += $note->evaluation->note_max ?? 20;
                                    }
                                    $average = $totalMaxPoints > 0 ? ($totalPoints / $totalMaxPoints) * 20 : 0;
                                @endphp
                                <span class="matiere-average">{{ __('app.moyenne') }}: {{ number_format($average, 2) }}/20</span>
                            </div>
                            
                            <div class="notes-table-wrapper">
                                <table class="table table-bordered notes-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('app.date') }}</th>
                                            <th>{{ __('app.type') }}</th>
                                            <th>{{ __('app.note') }}</th>
                                            <th>{{ __('app.sur_20') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($matiereNotes as $note)
                                            <tr>
                                                <td>{{ $note->evaluation->date->format('d/m/Y') }}</td>
                                                <td>{{ __('app.'.$note->evaluation->type) }}</td>
                                                <td>{{ number_format($note->note, 2) }}/{{ $note->evaluation->note_max }}</td>
                                                <td>{{ number_format(($note->note / $note->evaluation->note_max) * 20, 1) }}/20</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Statistics -->
                    <div class="statistics">
                        <div class="stat-grid">
                            <div class="stat-item">
                                <div class="stat-value">{{ number_format($statistics['overall_average'] ?? 0, 2) }}/20</div>
                                <div class="stat-label">{{ __('app.moyenne_generale') }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $statistics['passed_notes'] ?? 0 }}/{{ $statistics['total_notes'] ?? 0 }}</div>
                                <div class="stat-label">{{ __('app.notes_superieures_ou_egales') }} 10/20</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $statistics['mention'] ?? 'N/A' }}</div>
                                <div class="stat-label">{{ __('app.mention') }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('app.aucune_note_disponible') }}</h5>
                        <p class="text-muted">{{ __('app.aucune_note_trouvee') }}</p>
                    </div>
                @endif
                
                <!-- Footer -->
                <div class="text-center mt-4 pt-4 border-top">
                    <small class="text-muted">
                        {{ __('app.document_generated_on') }} {{ date('d/m/Y à H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function changeYear() {
        const year = document.getElementById('yearSelect').value;
        const trimestre = document.getElementById('trimestreSelect').value;
        let url = `{{ route('public.transcript.show', $etudiant->matricule) }}`;
        
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (trimestre) url += `/${trimestre}`;
        
        if (params.toString()) {
            url += `?${params.toString()}`;
        }
        
        window.location.href = url;
    }

    function changeTrimestre() {
        const year = document.getElementById('yearSelect').value;
        const trimestre = document.getElementById('trimestreSelect').value;
        let url = `{{ route('public.transcript.show', $etudiant->matricule) }}`;
        
        if (trimestre) {
            url += `/${trimestre}`;
        }
        
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        
        if (params.toString()) {
            url += `?${params.toString()}`;
        }
        
        window.location.href = url;
    }
    </script>
</body>
</html>
