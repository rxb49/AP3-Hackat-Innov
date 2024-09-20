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

        // Affichage de la vue, avec les données récupérées
        return view('main.home', [
            'hackathon' => $hackathon,
            'organisateur' => $hackathon->organisateur,
        ]);
    }

    public function nbInscrit(){
         $hackathon = Hackathon::getActiveHackathon();
         $valeursFiltre = Inscrire::where('idhackhathon', $hackathon )->orderBy('idequipe');
         return view('main.home', [
         'nbInscrit' => $valeursFiltre,
     ]);
    }

    /**
     * Retourne la page "À propos"
     */
    public function about()
    {
        return view('main.about');
    }

    public function membres()
    {
        return view('main.membres', ['equipe' => 'idequipe']);
    }
}
