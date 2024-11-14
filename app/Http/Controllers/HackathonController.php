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
    public function join(Request $request)
{
    // Si l'équipe n'est pas connectée, on redirige vers la page de connexion
    if (!SessionHelpers::isConnected()) {
        return redirect("/login")->withErrors(['errors' => "Vous devez être connecté pour accéder à cette page."]);
    }

    // Récupération de l'équipe connectée
    $equipe = SessionHelpers::getConnected();
    $idh = $request->get('idh'); // ID du hackathon actif passé en paramètre

    try {
        // Rechercher une inscription existante pour ce hackathon et cette équipe
        $inscription = Inscrire::where('idhackathon', $idh)
                                ->where('idequipe', $equipe->idequipe)
                                ->first();

        if ($inscription) {
            // Si l'équipe était déjà inscrite puis désinscrite, mettre à jour l'inscription
            if ($inscription->dateinscription === null && $inscription->datedesinscription !== null) {
                $inscription->dateinscription = now();
                $inscription->datedesinscription = null; // Réinitialiser la date de désinscription
                $inscription->save();
            } else {
                // L'équipe est déjà inscrite activement, redirection avec message
                return redirect("/me")->withErrors(['errors' => "Vous êtes déjà inscrit à ce hackathon."]);
            }
        } else {
            // Si l'équipe n'a jamais été inscrite, créer une nouvelle inscription
            $inscription = new Inscrire();
            $inscription->idhackathon = $idh;
            $inscription->idequipe = $equipe->idequipe;
            $inscription->dateinscription = now();
            $inscription->datedesinscription = null;
            $inscription->save();
        }

        // Récupération du hackathon pour envoyer les informations dans l'email
        $hackathon = Hackathon::find($idh);

        // Envoyer un email de confirmation à l'équipe
        EmailHelpers::sendEmail($equipe->login, "Inscription de votre équipe", "email.join-hackhathon", [
            'equipe' => $equipe,
            'hackathon' => $hackathon
        ]);

        // Redirection vers la page de l'équipe avec un message de succès
        return redirect("/me")->with('success', "Inscription réussie, vous faites maintenant partie du hackathon.");
        
    } catch (\Exception $e) {
        // En cas d'erreur, redirection avec un message d'erreur détaillé
        return redirect("/")->withErrors(['errors' => "Une erreur est survenue lors de l'inscription au hackathon. Veuillez réessayer."]);
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
        $hackathon = Hackathon::find($idh);

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
                EmailHelpers::sendEmail($equipe->login, "Désinscription de votre équipe", "email.leave-hackathon", ['equipe' => $equipe, 'hackathon' => $hackathon ]);

                // Redirection vers la page de l'équipe avec un message de succès
                return redirect("/me")->with('success', "Vous avez quitté le hackathon avec succès.");
            } else {
                return redirect("/me")->withErrors(['errors' => "Votre équipe n'est pas inscrite à ce hackathon."]);
            }
        } catch (\Exception $e) {
            // Redirection vers la page d'accueil avec un message d'erreur
            return redirect("/me")->withErrors(['errors' => "Une erreur est survenue lors de la désinscription du hackathon." . $e->getMessage()]);
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

    public function listHackathonByEquipe() {
        $equipe = SessionHelpers::getConnected();
        $ide = $equipe->idequipe;
        
        $inscrire = Inscrire::where('idequipe', $ide)->get();
    
        if ($inscrire->isEmpty()) {
            $allhackathon = Hackathon::paginate(5);
            return view('main.archive')
                ->with('alert', 'Aucune inscription trouvée pour cette équipe.')
                ->with('hackathon', $allhackathon);
        }
    
        $hackathon = Hackathon::whereIn('idhackathon', $inscrire->pluck('idhackathon'))
                              ->paginate(5);
                        
        return view('main.archive', ['hackathon' => $hackathon, 'connected' => $equipe]);
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
