@extends('layouts.app')

@section('title', ' - API Membres')

@section('content')
    <div class="d-flex flex-column justify-content-center min-vh-100 align-items-center">
        <div class="card col-xl-7  col-lg-9 col-md-10 col-12">
            <div class="card-body">
                <h5 class="card-title">
                    Liste des membres

                    @if ($lequipe != null)
                        de l'équipe « {{ $lequipe->nomequipe }} »
                    @endif


                </h5>
                <table class="table card-text">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Email</th>
                        <th scope="col">Téléphone</th>
                        <th scope="col">Date de naissance</th>
                        <th scope="col">Lien Portfolio</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $membre)
                        <tr>
                            <th><?= $membre->idmembre ?></th>
                            <td><?= $membre->nom ?></td>
                            <td><?= $membre->prenom ?></td>
                            <td><?= $membre->email ?></td>
                            <td><?= $membre->telephone ?></td>
                            <td><?= $membre->datenaissance ?></td>
                            <td><?= $membre->lienportfolio ?></td>
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
                    <div>
                        L'ensemble des membres :
                        <code>
                            /api/membre/all
                        </code>
                    </div>
                    <div>
                        Les membres par équipe :
                        <code>
                            /api/membre/{idequipe}
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
