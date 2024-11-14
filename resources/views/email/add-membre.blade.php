<!DOCTYPE html>
<html>

<head>
    <title>Bienvenue sur Hackat'innov</title>
</head>

<body>
    <h1>Vous êtes bien ajouter à l'équipe {{ $equipe->nomequipe }}</h1>
    <p>Cher {{ $membre->nom }} {{ $membre->prenom }},</p>
    <p>Vous pourrez désormais participer à différents hackathons sous le nom de l'équipe {{ $equipe->nomequipe }}</p>
    <p>Cordialement,</p>
    <p>Votre équipe de Hackat'innov</p>
</body>

</html>
