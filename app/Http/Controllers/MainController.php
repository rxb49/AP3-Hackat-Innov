<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\Inscrire;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Retourne la page d'accueil
     */
    public function home()
    {
        // Récuération du hackathon actif (celui en cours)
        $hackathon = Hackathon::getActiveHackathon();
        // Récupération du nb d'équipe inscrite au hackathon
        $nbInscrit = Inscrire::getNbInscrit($hackathon);
        // Affichage de la vue, avec les données récupérées
        return view('main.home', [
            'hackathon' => $hackathon,
            'nbInscrit' => $nbInscrit,
            'organisateur' => $hackathon->organisateur,
        ]);
    }

    /**
     * Retourne la page "À propos"
     */
    public function about()
    {
        return view('main.about');
    }

    public function archive()
    {
        return view('main.archive');
    }

    public function membres()
    {
        return view('main.membres', ['equipe' => 'idequipe']);
    }
}
