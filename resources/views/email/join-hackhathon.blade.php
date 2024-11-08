<!DOCTYPE html>
<html>

<head>
    <title>Bienvenue sur Hackat'innov</title>
</head>

<body>
    <h1>Vous êtes bien inscrit au hackathon {{ $hackathon->thematique }}</h1>
    <p>Cher {{ $equipe->nomequipe }},</p>
    <p>Le hackhathon commencera le {{ $hackathon->dateheuredebuth }} et finira le {{ $hackathon->dateheurefinh }}</p>
    <p>Le hackhathon se passera à {{ $hackathon->lieu }}</p>
    <p>Cordialement,</p>
    <p>Votre équipe de Hackat'innov</p>
</body>

</html>
