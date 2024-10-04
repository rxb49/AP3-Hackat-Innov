@extends('layouts.app')

@section('title', ' - Mon équipe')

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg fullContainer">

    <div class="card cardRadius">
        <div class="card-body">
            <!-- Affichage message flash de type "success" -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Affichage message flash de type "error" -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="list-unstyled text-start m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3>Bienvenue "{{ $connected->nomequipe }}"</h3>

            @if ($hackathon != null)
                <h5>Votre équipe est inscrite au Hackathon <br><br> « {{ $hackathon->thematique }} »</h5>
                <br/>
                <img src="{{ $hackathon->affiche }}" alt="Affiche de l'évènement." class="w-50"/>
            @else
                <p>
                    Vous ne participez à aucun évènement.
                </p>
            @endif

        </div>

        <div class="card-actions">
            <a href="/modifEquipe" class="btn btn-success btn-small">Modifier l'équipe</a>
            <a href="/logout" class="btn btn-danger btn-small">Déconnexion</a>
        </div>

    </div>

    <div class="card cardRadius mt-3">
        <div class="card-body">
            <h3 class="text-start">Membres de votre équipe</h3>
            <ul class="p-0 m-0 mb-2">
                @foreach ($membres as $m)
                    <li class="member">🧑‍💻 {{ "{$m->nom} {$m->prenom}" }}
                        <!-- Formulaire pour supprimer un membre -->
                        <form action="{{ route('deleteMembre', $m->idmembre) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-small justify-content-end" onclick="return confirm('Êtes-vous sûr de vouloir supprimer {{ $m->nom }} {{ $m->prenom }} ?');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg>
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <form method="post" class="row g-1" action="me">
                @csrf
                    <input required type="text" placeholder="Nom" name="nom" class="form-control"/>
                    <input required type="text" placeholder="Prénom" name="prenom" class="form-control"/>
                    <input required type="email" placeholder="Email" name="email" class="form-control"/>
                    <input required type="tel" placeholder="Téléphone" name="telephone" class="form-control"/>
                    <input required type="date" placeholder="Date de naissance" name="datenaissance" class="form-control"/>
                    <input required type="text" placeholder="Lien du portfolio" name="lienportfolio" class="form-control"/>
                    <input type="submit" value="Ajouter" class="btn btn-success d-block w-100"/>
            </form>
        </div>
    </div>

</div>
@endsection
