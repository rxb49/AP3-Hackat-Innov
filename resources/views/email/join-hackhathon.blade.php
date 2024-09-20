<!DOCTYPE html>
<html>

<head>
    <title>Bienvenue sur Hackat'innov</title>
</head>

<body>
    <h1>Vous êtes bien inscrit au hackathon {{ $hackhathon->thematique}}</h1>
    <p>Cher {{ $equipe->nomequipe }},</p>
    <p>Le hackhathon commencera le {{ $hackhathon->dateheuredebuth}} et finira le {{ $hackhathon->dateheurefinh}}</p>
    <p>Le hackhathon se passera à {{ $hackhathon->lieu}}</p>
    <p>Cordialement,</p>
    <p>Votre équipe de Hackat'innov</p>
</body>

</html>
