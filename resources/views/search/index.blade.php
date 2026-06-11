@extends('layouts.dashboard')

@section('title', 'Recherche')

@section('content')
<style>
    strong {
    background-color: yellow;
    font-weight: bold;
}
</style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('app.recherche') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('recherche') }}" method="GET">
                            <div class="input-group mb-3">
                                <input type="text" name="query" class="form-control" placeholder="{{ __('app.rechercher') }}..."
                                    value="{{ $query }}">
                                <select name="category" class="form-select">
                                    <option value="etudiant" {{ $category === 'etudiant' ? 'selected' : '' }}"> {{ __('app.etudiant') }}
                                    </option>
                                    <option value="enseignant" {{ $category === 'enseignant' ? 'selected' : '' }}"> {{ __('app.enseignant') }}
                                    </option>
                                    <option value="note" {{ $category === 'note' ? 'selected' : '' }}"> {{ __('app.note') }}</option>
                                    <option value="course" {{ $category === 'course' ? 'selected' : '' }}"> {{ __('app.cours') }}</option>
                                    <option value="evaluation" {{ $category === 'evaluation' ? 'selected' : '' }}"> {{ __('app.evaluation') }}
                                    </option>
                                </select>
                                <button type="submit" class="btn btn-primary">{{ __('app.rechercher') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('app.resultats') }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($results->isEmpty())
                            <p class="text-muted">{{ __('app.aucun_resultat_trouve') }}</p>
                        @else
                            <ul class="list-group">
                                @foreach ($results as $result)
                                    <li class="list-group-item">
                                        @if ($category === 'etudiant')
                                            <a href="{{ url('/etudiant/' . $result->id_etudiant) }}"
                                                class="d-flex align-items-center" title="{{ __('app.afficher_etudiant') }}">
                                                <span class="fw-bold">
                                                    {!! str_ireplace($query, '<strong>'.$query.'</strong>', 'E'.$result->id_etudiant ) !!} |
                                                    {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->nom) !!} 
                                                    {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->prenom) !!}
                                                </span>
                                                
                                            </a>
                                            <p class="text-muted">
                                                Classe: {!! str_ireplace($query, '<span class="text-primary"><strong>'.$query.'</strong></span>', $result->classe->niveau) !!} |
                                                Date de naissance: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->date_naissance) !!} |
                                                Adresse: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->adresse) !!} |
                                                Telephone: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->telephone) !!}
 
                                            </p>
                                            @elseif ($category === 'enseignant')
                                            <a href="{{ url('/enseignant/' . $result->id_enseignant) }}" class="d-block" title="{{ __('app.afficher_enseignant') }}">
                                                <span class="fw-bold">
                                                    {!! str_ireplace($query, '<strong>'.$query.'</strong>', 'E'.$result->id_enseignant ) !!} |
                                                    {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->nom) !!} 
                                                    {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->prenom) !!}
                                                </span>
                                            </a>
                                            <p class="text-muted">
                                                Classe enseigne: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->classe->niveau) !!} |
                                                Matiere: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->matiere) !!} |
                                                Email: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->email) !!} |
                                                Telephone: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->telephone) !!}
                                            </p>
                                        @elseif ($category === 'note')
                                        <span class="fw-bold">
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', 'E'.$result->etudiants->id_etudiant ) !!} |
                                            {!! str_ireplace($query, '<span class="text-primary"><strong>'.$query.'</strong></span>', $result->etudiants->nom) !!} 
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->etudiants->prenom) !!} 
                                            
                                        </span>
                                        <p class="text-muted">
                                            Matiere: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->matiere) !!} |
                                            Type: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->type) !!} |
                                            Note: {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->note.'/20') !!}
                                        </p>
                                        @elseif ($category === 'course')
                                        <span class="fw-bold">
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->jour) !!} | 
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->date_debut ) !!} -
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->date_fin) !!} 
                                            
                                        </span>
                                        <p class="text-muted">
                                                Classe: {!! str_ireplace($query, '<span class="text-primary"><strong>'.$query.'</strong></span>', $result->classe->niveau) !!} |
                                                Matiere: {!! str_ireplace($query, '<span class="text-primary"><strong>'.$query.'</strong></span>', $result->matiere) !!}
                                        </p>
                                        @elseif ($category === 'evaluation')
                                        <span class="fw-bold">
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->date) !!} | 
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->date_debut ) !!} -
                                            {!! str_ireplace($query, '<strong>'.$query.'</strong>', $result->date_fin) !!} 
                                            
                                        </span>
                                        <p class="text-muted">
                                                Classe: {!! str_ireplace($query, '<span class="text-primary"><strong>'.$query.'</strong></span>', $result->classe->niveau) !!} |
                                                Matiere: {!! str_ireplace($query, '<span class="text-primary"><strong>'.$query.'</strong></span>', $result->matiere) !!}
                                        </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
