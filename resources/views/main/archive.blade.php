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

 if (SessionHelpers::isConnected()) { ?>
    <a class="btn bg-green m-2 button-home" href="/archiveByEquipe?ide=<?= $connected->nomequipe ?>">Hackathon de l'équipe</a><br>
    <?php } ?>
    <?php foreach ($hackathon as $h): ?>
        <div class="row hackathon-item mb-5">
            <div class="col-12 col-md-9 d-flex">
                <img src="<?= $h->affiche ?>" class="affiche d-md-block d-none" alt="Affiche de l'évènement." style="min-width: 450px; margin-right: 20px;">
                <div class="px-5">
                    <h2><?= $h->thematique ?></h2>
                    <p><?= nl2br($h->objectifs) ?></p>
                    <p><?= nl2br($h->conditions) ?></p>

                    <div class="card w-100 mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Informations :</h5>
                            <p class="card-text">
                                <em>Date :</em> <?= date_create($h->dateheuredebuth)->format("d/m/Y H:i") ?>
                                au <?= date_create($h->dateheurefinh)->format("d/m/Y H:i") ?><br>
                                <em>Lieu :</em> <?= $h->ville ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Lien de pagination -->
    <div class="d-flex justify-content-center">
        {{ $hackathon->links('pagination::bootstrap-4') }}
    </div>
</div>


@endsection
