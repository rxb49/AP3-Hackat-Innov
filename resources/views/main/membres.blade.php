<!DOCTYPE html>
<html>
<head>
    <title>Membres de l'équipe {{ $equipe->nomequipe }}</title>
</head>
<body>

    <h1>Membres de l'équipe {{ $equipe->nomequipe }}</h1>

    @if($membres->isEmpty())
        <p>Aucun membre trouvé dans cette équipe.</p>
    @else
        <ul>
            @foreach($membres as $membre)
                <li>{{ $membre->prenom }} {{ $membre->nom }}</li>
            @endforeach
        </ul>
    @endif

    <a href="{{ url()->previous() }}">Retour</a>

</body>
</html>