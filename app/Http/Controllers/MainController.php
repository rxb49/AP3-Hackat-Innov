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
        // Récupération du hackathon actif (celui en cours)
        $hackathon = Hackathon::getActiveHackathon();
    
        // Vérification si un hackathon actif existe
        if ($hackathon) {
            // Récupération du nombre d'équipes inscrites au hackathon
            $nbInscrit = self::getNbInscrit($hackathon->idhackathon);
        } else {
            $nbInscrit = 0; // Aucun hackathon actif
        }
    
        // Affichage de la vue avec les données récupérées
        return view('main.home', [
            'hackathon' => $hackathon,
            'organisateur' => $hackathon ? $hackathon->organisateur : null,
            'nbInscrit' => $nbInscrit,
        ]);
    }

    public static function getNbInscrit($idHackathon) : int
    {
        // Compter les équipes inscrites activement au hackathon (dateinscription non null et datedesinscription null)
        $nbInscrit = Inscrire::where('idhackathon', $idHackathon)
                            ->whereNotNull('dateinscription')
                            ->whereNull('datedesinscription')
                            ->count();

        return $nbInscrit;
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
