@extends('layouts.app')

@section('title', ' - Bienvenue')

@section('custom-css')
    <link href="/css/home.css" rel="stylesheet"/>
@endsection

@section('content')
<div class="container mt-5">

    <a class="btn bg-green m-2 button-home" href="/archive">Tout afficher</a>
    <a class="btn bg-green m-2 button-home" href="/passedArchive">Hackathon passé</a>
    <a class="btn bg-green m-2 button-home" href="/incomingArchive">Hackathon futur</a>
    <?php
    use App\Utils\SessionHelpers;

    // if (SessionHelpers::isConnected()) { ?>
         <!--<a class="btn bg-green m-2 button-home" href="/archiveByEquipe?ide=<//?= $connected->nomequipe ?>">Hackathon de l'équipe</a><br>
    // <//?php } ?>-->

    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Thématique</th>
                <th>Objectifs</th>
                <th>Conditions</th>
                <th>Date de début</th>
                <th>Date de fin</th>
                <th>Lieu</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hackathon as $h): ?>
                <tr>
                    <td><?= $h->thematique ?></td>
                    <td><?= nl2br($h->objectifs) ?></td>
                    <td><?= nl2br($h->conditions) ?></td>
                    <td><?= date_create($h->dateheuredebuth)->format("d/m/Y H:i") ?></td>
                    <td><?= date_create($h->dateheurefinh)->format("d/m/Y H:i") ?></td>
                    <td><?= $h->ville ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Lien de pagination -->
    <div class="d-flex justify-content-center">
        {{ $hackathon->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
