@extends('layouts.admin')

@section('title', ' - Login')

@section('custom-css')
    <link href="/css/home.css" rel="stylesheet"/>
@endsection

@section('content')

<table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prototype</th>
                <th>email</th>
                <th>telecharger les données</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipe as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e->nomequipe) ?></td>
                    <td><?= htmlspecialchars($e->lienprototype) ?></td>
                    <td><?= htmlspecialchars($e->login) ?></td> 
                    <td><a class="btn bg-green m-2 button-home" href="/download?idh=<?= $e->idequipe ?>">
                            Télécharger les données
                        </a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $equipe->links('pagination::bootstrap-4') }}
    </div>

@endsection
