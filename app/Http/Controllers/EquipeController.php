<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Hackathon;
use App\Models\Inscrire;
use App\Models\Membre;
use App\Utils\EmailHelpers;
use App\Utils\SessionHelpers;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    /**
     * Affiche la page de connexion.
     *
     * L'équipe se connecte avec son email et son mot de passe.
     * Le formulaire soumet les données à la route connect (POST).
     */
    public function login()
    {
        return view('equipe.login');
    }

    /**
     * Méthode de connexion de l'équipe.
     * Vérifie les informations de connexion et connecte l'équipe.
     */
    public function connect(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
                'email' => 'Le champ :attribute doit être une adresse email valide.',
            ],
            [
                'email' => 'email',
                'password' => 'mot de passe',
            ]
        );

        // Récupération de l'équipe avec l'email fourni
        $equipe = Equipe::where('login', $validated['email'])->first();

        // Si l'équipe n'existe pas, on redirige vers la page de connexion avec un message d'erreur
        if (!$equipe) {
            return redirect("/login")->withErrors(['errors' => "Aucune équipe n'a été trouvée avec cet email."]);
        }

        // Si le mot de passe est incorrect, on redirige vers la page de connexion avec un message d'erreur
        // Le message d'erreur est volontairement vague pour des raisons de sécurité
        // En cas d'erreur, on ne doit pas donner d'informations sur l'existence ou non de l'email
        if (!password_verify($validated['password'], $equipe->password)) {
            return redirect("/login")->withErrors(['errors' => "Aucune équipe n'a été trouvée avec cet email."]);
        }

        // Connexion de l'équipe
        SessionHelpers::login($equipe);

        // Redirection vers la page de profil de l'équipe
        return redirect("/me");
    }

    /**
     * Méthode de création d'une équipe.
     * Affiche le formulaire de création d'équipe.
     */
    public function create(Request $request)
    {
        // Si l'équipe est déjà connectée, on la redirige vers sa page de profil
        if (SessionHelpers::isConnected()) {
            return redirect("/me");
        }

        // Si le formulaire n'a pas été soumis, on affiche le formulaire de création d'équipe
        if (!$request->isMethod('post')) {
            return view('equipe.create', []);
        }

        // Sinon, on traite les données du formulaire
        // Validation des données, on vérifie que les champs sont corrects.
        $request->validate(
            [
                'nom' => 'required|string|max:255',
                'lien' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:EQUIPE,login',
                'password' => 'required|string|min:8',
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
                'string' => 'Le champ :attribute doit être une chaîne de caractères.',
                'max' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
                'email' => 'Le champ :attribute doit être une adresse email valide.',
                'unique' => 'Cette adresse :attribute est déjà utilisée.',
                'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
            ],
            [
                'nom' => 'nom',
                'lien' => 'lien',
                'email' => 'email',
                'password' => 'mot de passe',
            ]
        );

        // Récupération du hackathon actif
        $hackathon = Hackathon::getActiveHackathon();

        // Si aucun hackathon n'est actif, on redirige vers la page de création d'équipe avec un message d'erreur
        if (!$hackathon) {
            return redirect("/create-team")->withErrors(['errors' => "Aucun hackathon n'est actif pour le moment. Veuillez réessayer plus tard."]);
        }

        try {
            // Création de l'équipe
            $equipe = new Equipe();
            $equipe->nomequipe = $request->input('nom');
            $equipe->lienprototype = $request->input('lien');
            $equipe->login = $request->input('email');
            $equipe->password = bcrypt($request->input('password'));
            $equipe->save();

            // Envoi d'un email permettant de confirmer l'inscription
            EmailHelpers::sendEmail($equipe->login, "Création de votre équipe", "email.create-team", ['equipe' => $equipe]);

            // Connexion de l'équipe
            SessionHelpers::login($equipe);

            // L'équipe rejoindra le hackathon actif.
            // On crée une inscription pour l'équipe (table INSCRIRE)
            Inscrire::create([
                'idequipe' => $equipe->idequipe,
                'idhackathon' => $hackathon->idhackathon,
                'dateinscription' => date('Y-m-d H:i:s'),
            ]);

            // Redirection vers la page de profil de l'équipe avec un message de succès
            return redirect("/me")->with('success', "Votre équipe a bien été créée. Vérifiez votre boîte mail pour confirmer votre inscription.");
        } catch (\Exception $e) {
            // Redirection vers la page de création d'équipe avec un message d'erreur
            return redirect("/create-team?idh=" . $request->idh)->withErrors(['errors' => "Une erreur est survenue lors de la création de votre équipe."]);
        }

    }

    /**
     * Méthode de déconnexion, vide la session et redirige vers la page d'accueil.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        SessionHelpers::logout();
        return redirect()->route('home');
    }

        /**
     * Méthode de modification d l'équipe, vide la session et redirige vers la page d'accueil.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function modifEquipe()
    {
        if(SessionHelpers::isConnected()){
            $equipe = SessionHelpers::getConnected();

            return view('equipe.modifEquipe', ['equipe' => $equipe]);
        }
    }


    public function modif(Request $request)
{
    // Vérifier que l'équipe est connectée
    if (!SessionHelpers::isConnected()) {
        return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette page."]);
    }

    // Récupération de l'équipe connectée
    $equipe = SessionHelpers::getConnected();

    // Si la requête est de type GET, on affiche le formulaire
    if ($request->isMethod('get')) {
        return view('equipe.modifEquipe', ['equipe' => $equipe]);
    }

    // Si la requête est POST, on traite la modification
    if ($request->isMethod('post')) {
        // Validation des données du formulaire
        $validated = $request->validate(
            [
                'nom' => 'required|string|max:255',
                'lien' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:EQUIPE,login,' . $equipe->idequipe . ',idequipe', // Ignore l'équipe connectée pour l'unicité
                'password' => 'nullable|string|min:8|confirmed', // Mot de passe (nullable, mais si présent, il doit être confirmé)
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
                'string' => 'Le champ :attribute doit être une chaîne de caractères.',
                'max' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
                'email' => 'Le champ :attribute doit être une adresse email valide.',
                'unique' => 'Cette adresse :attribute est déjà utilisée.',
                'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
                'confirmed' => 'Les mots de passe ne correspondent pas.', // Validation pour la confirmation du mot de passe
            ],
            [
                'nom' => 'nom de l\'équipe',
                'lien' => 'lien de l\'équipe',
                'email' => 'email',
                'password' => 'mot de passe',
            ]
        );

        try {
            // Mise à jour des informations de l'équipe
            $equipe->nomequipe = $validated['nom'];
            $equipe->lienprototype = $validated['lien'];
            $equipe->login = $validated['email'];

            // Si un mot de passe est fourni, on le met à jour
            if (!empty($validated['password'])) {
                $equipe->password = bcrypt($validated['password']);
            }

            $equipe->save();

            // Redirection avec un message de succès
            return redirect("/me")->with('success', "Les informations de votre équipe ont été mises à jour avec succès.");
        } catch (\Exception $e) {
            // Gestion des erreurs
            return redirect("/modif-team")->withErrors(['errors' => "Une erreur est survenue lors de la mise à jour des informations de l'équipe."]);
        }
    }
}


    /**
     * Méthode de visualisation de la page de profil de l'équipe.
     * Permet de voir les informations de l'équipe, les membres, et d'ajouter des membres.
     */
    public function me()
    {
        // Si l'équipe n'est pas connectée, on la redirige vers la page de connexion
        if (!SessionHelpers::isConnected()) {
            return redirect("/login");
        }

        // Récupération de l'équipe connectée
        $equipe = SessionHelpers::getConnected();

        // Récupération des membres de l'équipe
        $membres = $equipe->membres;

        // Récupération du hackathon ou l'équipe est inscrite
        $hackathon = $equipe->hackathons()->first();

        // Membre de l'équipe,
        // Membre::where('idequipe', $equipe->idequipe)->get(); // Ancienne méthode
        // Voir la méthode membres() dans le modèle Equipe équivalente à la ligne précédente.
        $membres = $equipe->membres()->get();

        return view('equipe.me', ['connected' => $equipe, 'membres' => $membres, 'hackathon' => $hackathon, 'membres' => $membres]);
    }


    public function deleteMembre(Request $request, $idMembre)
    {
        // Vérifie que l'équipe est connectée
        if (!SessionHelpers::isConnected()) {
            return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette fonctionnalité."]);
        }

        // Récupération de l'équipe connectée
        $equipe = SessionHelpers::getConnected();

        // Recherche du membre par ID
        $membre = Membre::where('idmembre', $idMembre)->where('idequipe', $equipe->idequipe)->first();

        // Vérifie si le membre existe
        if (!$membre) {
            return redirect("/me")->withErrors(['errors' => "Le membre n'a pas été trouvé."]);
        }

        try {
            // Suppression du membre
            $membre->delete();

            // Redirection avec un message de succès
            return redirect("/me")->with('success', "Le membre a bien été supprimé de votre équipe.");
        } catch (\Exception $e) {
            // Gestion des erreurs
            return redirect("/me")->withErrors(['errors' => "Une erreur est survenue lors de la suppression du membre."]);
        }
    }


    /**
     * Méthode d'ajout d'un membre à l'équipe.
     */
    public function addMembre(Request $request)
{
    // Récupération de l'équipe connectée
    $equipe = SessionHelpers::getConnected();



    // Vérification que l'équipe existe
    if (!$equipe || !$equipe->idequipe) {
        return redirect("/me")->withErrors(['errors' => "Aucune équipe n'a été trouvée pour l'utilisateur connecté."]);
    }

    // Validation des données
    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'telephone' => 'required|string|max:128',
        'datenaissance' => 'required|date|max:255',
        'lienportfolio' => 'required|string|max:255',


    ], [
        'required' => 'Le champ :attribute est obligatoire.',
        'string' => 'Le champ :attribute doit être une chaîne de caractères.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'date' => 'Le champ :attribute doit être une date',
        'max' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
    ], [
        'nom' => 'nom',
        'prenom' => 'prénom',
        'email' => 'email',
        'telephone' => 'telephone',
        'datenaissance' => 'datenaissance',
        'lienportfolio' => 'lienportfolio',
    ]);

    try {
        // Création du membre
        $membre = new Membre();
        $membre->nom = $request->input('nom');
        $membre->prenom = $request->input('prenom');
        $membre->email = $request->input('email');
        $membre->telephone = $request->input('telephone');
        $membre->datenaissance = $request->input('datenaissance');
        $membre->lienportfolio = $request->input('lienportfolio');
        $membre->idequipe = $equipe->idequipe;
        $membre->save();


        // Envoie d'un mail d'information
        EmailHelpers::sendEmail($membre->email, "Ajout dans l'équipe", "email.add-membre", ['equipe' => $equipe, 'membre' => $membre]);
        return redirect("/me")->with('success', "Le membre a bien été ajouté à votre équipe.");
    } catch (\Exception $e) {
        return redirect("/me")->withErrors(['errors' => "Une erreur est survenue lors de l'ajout du membre à votre équipe."]);
    }
}


    function detailEquipe(Request $request)
    {
        if (!SessionHelpers::isConnected()) {
            return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette page."]);
        }

        try{
            $data = Membre::all();

        // Initialisation de la variable lequipe
        $lequipe = null;

        if($request->has('ide')) {
            // Récupération de l'équipe spécifiée
            $lequipe = Equipe::find($request->input('ide'));

            // Récupération des membres de l'équipe spécifiée
            $data = Membre::where('idequipe', $request->input('ide'))->get();
        }

        return view('doc.membres', ['data' => $data, 'lequipe' => $lequipe]);
        }catch(\Exception $e){
            return redirect("/")->withErrors(['errors' => "Une erreur est survenue lors de l'inscription au hackathon."]);
        }
    }

    function telecharger(){
        
    }

}
