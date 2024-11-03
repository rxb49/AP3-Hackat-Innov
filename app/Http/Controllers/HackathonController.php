<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Hackathon;
use App\Models\Inscrire;
use App\Utils\EmailHelpers;
use Illuminate\Http\Request;
use App\Utils\SessionHelpers;


class HackathonController extends Controller
{
    public function join(Request $request){
        // Si l'équipe n'est pas connectée, on redirige vers la page de connexion
        if (!SessionHelpers::isConnected()) {
            return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette page."]);
        }

        // Récupération de l'équipe connectée
        $equipe = SessionHelpers::getConnected();

        // Le hackathon actif est en paramètre de la requête (idh en GET).
        // À prévoir : récupérer l'id du hackathon actif depuis la base de données pour éviter les erreurs.

        // Récupération de l'id du hackathon actif
        $idh = $request->get('idh');
        
        try{
            // Inscription de l'équipe au hackathon
            $inscription = new Inscrire();
            $inscription->idhackathon = $idh;
            $inscription->idequipe = $equipe->idequipe;
            $inscription->dateinscription = date('Y-m-d H:i:s');
            $inscription->datedesinscription = null;
            $inscription->save();

            // TODO : envoyer un email de confirmation à l'équipe en utilisant la classe EmailHelpers, et la méthode sendEmail (exemple présent dans le contrôleur EquipeController)
            EmailHelpers::sendEmail($equipe->login, "Inscription de votre équipe", "email.join-hackhathon", ['equipe' => $equipe]);
            // Redirection vers la page de l'équipe
            return redirect("/me")->with('success', "Inscription réussie, vous faites maintenant partie du hackathon.");
        } catch (\Exception $e) {
            // Redirection vers la page d'accueil avec un message d'erreur
            return redirect("/")->withErrors(['errors' => "Une erreur est survenue lors de l'inscription au hackathon. Vous êtes déjà inscrit à ce hackhathon"]);
        }
    }

    public function quit(Request $request)
    {
        // Si l'équipe n'est pas connectée, on redirige vers la page de connexion
        if (!SessionHelpers::isConnected()) {
            return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette page."]);
        }

        // Récupération de l'équipe connectée
        $equipe = SessionHelpers::getConnected();

        // Récupération de l'id du hackathon à quitter
        $idh = $request->get('idh');

        try {
            // Vérification de l'inscription de l'équipe au hackathon
            $inscription = Inscrire::where('idhackathon', $idh)
                ->where('idequipe', $equipe->idequipe)
                ->first();

            // Si l'inscription existe, on la modifie
            if ($inscription) {
                // Mettre à jour les dates d'inscription et de désinscription
                $inscription->datedesinscription = date('Y-m-d H:i:s'); // Date et heure actuelle
                $inscription->dateinscription = null; // Mise à null de la date d'inscription
                $inscription->save();

                // Envoyer un email de confirmation à l'équipe pour l'informer du départ
                EmailHelpers::sendEmail($equipe->login, "Désinscription de votre équipe", "email.leave-hackathon", ['equipe' => $equipe]);

                // Redirection vers la page de l'équipe avec un message de succès
                return redirect("/me")->with('success', "Vous avez quitté le hackathon avec succès.");
            } else {
                return redirect("/me")->withErrors(['errors' => "Votre équipe n'est pas inscrite à ce hackathon."]);
            }
        } catch (\Exception $e) {
            // Redirection vers la page d'accueil avec un message d'erreur
            return redirect("/me")->withErrors(['errors' => "Une erreur est survenue lors de la désinscription du hackathon."]);
        }
    }

    public function list(){
        $hackathon = Hackathon::orderBy('dateheuredebuth', 'asc')->paginate(5);
        return view('main.archive', 
        ['hackathon' => $hackathon]);
    }

    public function listPassedHackathon() {
        $hackathon = Hackathon::where('dateheurefinh', '<=', now())
                    ->orderBy('dateheuredebuth', 'asc')
                    ->paginate(5);
                    
        return view('main.archive', ['hackathon' => $hackathon]);
    }

    public function listIncomingHackathon() {
        $hackathon = Hackathon::where('dateheurefinh', '>=', now())
                    ->orderBy('dateheuredebuth', 'asc')
                    ->paginate(5);
                    
        return view('main.archive', ['hackathon' => $hackathon]);
    }

    public function listHackathonByEquipe(Request $request) {
        $equipe = SessionHelpers::getConnected();
        $ide = $request->get('ide');
        dd($equipe);
        $hackathon = Inscrire::where('idequipe', $ide)
                    ->orderBy('dateheuredebuth', 'asc')
                    ->paginate(5);
                    
        return view('main.archive', ['hackathon2' => $hackathon, 'connected' => $equipe]);
    }


    public function commentaire(Request $request)
    {    
        // Vérifier si l'utilisateur est connecté
        if (!SessionHelpers::isConnected()) {
            return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette page."]);
        }

        try {
            // Initialiser les variables
            $data = collect();  // Liste de commentaires initialement vide
            $hackathon = null;  // Hackathon initialisé à null

            // Vérifier la présence du paramètre 'idh' dans la requête
            if ($request->has('idh')) {
                $idhackathon = $request->input('idh');

                // Récupérer le hackathon en fonction de l'id fourni
                $hackathon = Hackathon::find($idhackathon);

                if (!$hackathon) {
                    return redirect("/")->withErrors(['errors' => "Hackathon non trouvé."]);
                }

                // Récupérer les commentaires associés au hackathon
                $data = Commentaire::where('idhackathon', $idhackathon)->paginate(5);
            }

            // Renvoyer la vue avec les données
            return view('main.commentaire', ['data' => $data, 'hackathon' => $hackathon]);

        } catch (\Exception $e) {
            // Rediriger avec un message d'erreur en cas d'exception
            return redirect("/")->withErrors(['errors' => "Une erreur est survenue lors de la récupération des commentaires."]);
        }
    }



}
