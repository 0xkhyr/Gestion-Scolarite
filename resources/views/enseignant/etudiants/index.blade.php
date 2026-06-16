@extends('layouts.dashboard')

@section('title', __('app.mes_etudiants'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('app.mon_enseignement') }}</li>
    <li class="breadcrumb-item active">{{ __('app.mes_etudiants') }}</li>
@endsection
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            {{ __('app.mes_etudiants') }}
        </h1>
        @if($classes->count() > 1)
            <div class="badge bg-info fs-6">
                {{ $classes->count() }} {{ __('app.classes') }}
            </div>
        @endif
    </div>

    @if($classes->count() > 0)
        <!-- Classes Summary Cards -->
        @if($classes->count() > 1)
            <div class="row mb-4">
                @foreach($classes as $classe)
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            {{ $classe->nom_classe }}
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $classe->etudiants ? $classe->etudiants->count() : 0 }} {{ __('app.etudiants') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="h2 text-gray-300 mb-0">{{ $classe->etudiants ? $classe->etudiants->count() : 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    @if($classes->count() == 1)
                        {{ __('app.classe') }} : {{ $classes->first()->nom_classe }}
                    @else
                        {{ __('app.tous_mes_etudiants') }}
                    @endif
                </h6>
            </div>
            <div class="card-body">
                @if($students->count() > 0)
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('app.matricule') }}</th>
                                    <th>{{ __('app.nom') }}</th>
                                    <th>{{ __('app.prenom') }}</th>
                                    @if($classes->count() > 1)
                                        <th>{{ __('app.classe') }}</th>
                                    @endif
                                    <th>{{ __('app.genre') }}</th>
                                    <th>{{ __('app.date_de_naissance') }}</th>
                                    <th>{{ __('app.telephone') }}</th>
                                    <th>{{ __('app.email') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $student->matricule }}</span></td>
                                        <td>{{ $student->nom }}</td>
                                        <td>{{ $student->prenom }}</td>
                                        @if($classes->count() > 1)
                                            <td>
                                                @php
                                                    $studentClass = $classes->firstWhere('id_classe', $student->id_classe);
                                                @endphp
                                                <span class="badge bg-success">
                                                    {{ $studentClass ? $studentClass->nom_classe : 'N/A' }}
                                                </span>
                                            </td>
                                        @endif
                                        <td>
                                            <span class="badge bg-{{ $student->genre == 'M' ? 'info' : 'secondary' }}">
                                                {{ __('app.' . strtolower($student->genre)) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($student->date_naissance)->format('d/m/Y') }}</td>
                                        <td>{{ $student->telephone }}</td>
                                        <td>{{ $student->email ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <strong>{{ __('app.total') }} :</strong> {{ $students->count() }} {{ __('app.etudiant(s)') }}
                            @if($classes->count() > 1)
                                {{ __('app.dans') }} {{ $classes->count() }} {{ __('app.classes') }}
                            @else
                                {{ __('app.in_your_class') }}
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        {{ __('app.aucun_etudiant_trouve') }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            {{ __('app.aucune_classe_assignee') }}
        </div>
    @endif
</div>
@endsection
