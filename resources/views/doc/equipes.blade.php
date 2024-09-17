@extends('layouts.app')

@section('title', ' - API Équipes')

@section('content')
    <div class="d-flex flex-column justify-content-center min-vh-100 align-items-center">
        <div class="card col-xl-7  col-lg-9 col-md-10 col-12">
            <div class="card-body">
                <h5 class="card-title">
                    Liste des équipes
                    @if ($hackathon != null)
                        pour le hackathon « {{ $hackathon->thematique }} »
                    @endif
                </h5>
                <table class="table card-text">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Lien prototype</th>
                        <th scope="col">Nombre de participants</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $equipe)
                        <tr>
                            <th><?= $equipe->idequipe ?></th>
                            <td><?= $equipe->nomequipe ?></td>
                            <td><?= $equipe->lienprototype ?></td>
                            <td><?= $equipe->membres->count() ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary"
                                   href="/doc-api/membres?ide={{$equipe->idequipe}}">
                                    Les membres
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card col-xl-7  col-lg-9 col-md-10 col-12 mt-5">
            <div class="card-body">
                <h5 class="card-title">API JSON</h5>

                <div class="card-text">
                    Les équipes :
                    <code>
                        GET /api/equipe/all
                    </code>
                </div>

                <hr>

                <div class="card-text">
                    Les équipes pour un IdHackathon :
                    <code>
                        GET /api/hackathon/{idh}/equipe
                    </code>
                </div>

                <hr>

                <div>
                    Connexion en tant qu'équipe :
                    <code>
                        POST /api/equipe/auth
                    </code>

                    <div>
                        <h6>Paramètres :</h6>
                        <ul>
                            <li>login</li>
                            <li>password</li>
                        </ul>

                        <h6>Retour :</h6>
                        <code>
                            {
                            "success": 1,
                            "result": {
                            "uuid": "72cc6fdc-db66-492c-bb29-5c0a8e5331c4",
                            "idequipe": 3
                            }
                            }
                        </code>
                    </div>

                </div>

                <hr>

                <div>
                    Obtenir une équipe avec un TOKEN d'authentification :
                    <code>
                        GET /api/equipe/{token}
                    </code>
                </div>

                <hr>


                <div>
                    Créer une équipe :
                    <code>
                        POST /api/equipe/create
                    </code>
                    <div>
                        <h6>Paramètres :</h6>
                        <ul>
                            <li>nomequipe</li>
                            <li>lienprototype</li>
                            <li>nbparticipants</li>
                            <li>login</li>
                            <li>password</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
