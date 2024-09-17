<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Hackathon;
use App\Models\Membre;
use Illuminate\Http\Request;

class ApiDocController extends Controller
{
    /**
     * Documentation de l'API
     * Retourne la page d'accueil de la documentation de l'API
     */
    function liste()
    {
        return view('doc.list');
    }

    /**
     * Retourne la documentation de la partie API concernant les hackathons
     *
     */
    function listeHackathons()
    {
        // Récupération de tous les hackathons
        $data = Hackathon::all();

        // Affichage de la vue, avec les données récupérées
        return view('doc.hackathons', ['data' => $data]);
    }

    /**
     * Retourne la documentation de la partie API concernant les équipes
     * Si passé en paramètre, retourne les équipes inscrites à un hackathon spécifique (idh)
     */
    function listeEquipes(Request $request)
    {
        // Récupération de toutes les équipes
        $data = Equipe::all();

        // Initialisation de la variable hackathon
        $hackathon = null;

        /**
         * Si un hackathon est spécifié, on récupère les équipes inscrites à ce hackathon
         */
        if ($request->has('idh')) {
            // Récupération du hackathon spécifié
            $hackathon = Hackathon::find($request->input('idh'));

            // Récupération des équipes inscrites au hackathon
            $data = Equipe::getEquipesInHackhon($hackathon->idhackathon);
        }

        return view('doc.equipes', ['data' => $data, 'hackathon' => $hackathon]);
    }

    /**
     * Retourne la documentation de la partie API concernant les membres
     * Si passé en paramètre, retourne les membres d'une équipe spécifique (ide)
     */
    function listeMembres(Request $request)
    {
        // Récupération de tous les membres
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
    }
}
