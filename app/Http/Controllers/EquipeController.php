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
            EmailHelpers::sendEmail($equipe->login, "Inscription de votre équipe", "email.create-team", ['equipe' => $equipe]);

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

    /**
     * Méthode d'ajout d'un membre à l'équipe.
     */
    public function addMembre(Request $request)
    {
        // Ajout d'un membre à l'équipe
        $equipe = SessionHelpers::getConnected();

        // Validation des données, pour l'instant nous n'avons que NOM et PRENOM.
        // TODO : À l'avenir ajouter l'ensemble des champs nécessaires prévus dans la base de données.

        $request->validate(
            [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
                'string' => 'Le champ :attribute doit être une chaîne de caractères.',
                'max' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
            ],
            [
                'nom' => 'nom',
                'prenom' => 'prénom',
            ]
        );

        try {
            // Création du membre
            $membre = new Membre();
            $membre->nom = $request->input('nom');
            $membre->prenom = $request->input('prenom');
            $membre->idequipe = $equipe->idequipe;
            $membre->save();

            // TODO : envoyer un email de confirmation au membre en s'inspirant de la méthode create de EquipeController (emailHelpers::sendEmail)

            // Redirection vers la page de l'équipe
            return redirect("/me")->with('success', "Le membre a bien été ajouté à votre équipe.");
        } catch (\Exception $e) {
            // Redirection vers la page de l'équipe avec un message d'erreur
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
}
