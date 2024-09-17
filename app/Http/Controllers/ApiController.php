<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Hackathon;
use App\Models\Membre;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Retourne tous les hackathons présents dans la base de données.
     */
    function getAllHackathon()
    {
        return response()->json(Hackathon::all());
    }

    /**
     * Retourne le hackathon actif (celui qui est en cours)
     */
    function getActiveHackathon()
    {
        return response()->json(Hackathon::getActiveHackathon());
    }

    /**
     * Retourne les équipes inscrites à un hackathon.
     * @param $idh
     * @return \Illuminate\Http\JsonResponse
     */
    function getEquipeByHackathon($idh)
    {
        return response()->json(Equipe::getEquipesInHackhon($idh));
    }

    /**
     * Retourne tous les membres présents dans la base de données.
     * @return \Illuminate\Http\JsonResponse
     */
    function getAllMembres()
    {
        return response()->json(Membre::all());
    }

    /**
     * Retourne les membres d'une équipe.
     * @param $idequipe
     * @return \Illuminate\Http\JsonResponse
     */
    function getByEquipeId($idequipe)
    {
        return response()->json(Membre::where('idequipe', $idequipe)->get());
    }

    /**
     * Retourne toutes les équipes présentes dans la base de données.
     * @return \Illuminate\Http\JsonResponse
     */
    function getAllEquipe()
    {
        return response()->json(Equipe::all());
    }

    /**
     * Crée une équipe.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function createEquipe(Request $request)
    {
        $validated = $request->validate(
            [
                'nomequipe' => 'required',
                'lienprototype' => 'required',
                'nbparticipants' => 'required',
                'login' => 'required',
                'password' => 'required',
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
            ]
        );

        $equipe = new Equipe();
        $equipe->nomequipe = $validated['nomequipe'];
        $equipe->lienprototype = $validated['lienprototype'];
        $equipe->nbparticipants = $validated['nbparticipants'];
        $equipe->login = $validated['login'];
        $equipe->password = password_hash($validated['password'], PASSWORD_DEFAULT);
        $equipe->save();

        return response()->json($equipe);
    }

    /**
     * Authentifie une équipe.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function authAuthEquipe(Request $request)
    {
        $validated = $request->validate(
            [
                'login' => 'required',
                'password' => 'required',
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
            ]
        );

        $equipe = Equipe::where('login', $validated['login'])->first();

        if (!$equipe) {
            return response()->json(['error' => 'Aucune équipe n\'a été trouvée avec cet email.'], 404);
        }

        if (!password_verify($validated['password'], $equipe->password)) {
            return response()->json(['error' => 'Mot de passe incorrect.'], 401);
        }

        // Création d'un uuidv4
        $token = bin2hex(random_bytes(16));
        $equipe->tokens()->create(['uuid' => $token]);

        return response()->json(['token' => $token]);
    }

    /**
     * Retourne une équipe en fonction de son token.
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    function getByTokenEquipe($token)
    {
        $equipe = Equipe::getByToken($token);

        if (!$equipe) {
            return response()->json(['error' => 'Aucune équipe n\'a été trouvée avec ce token.'], 404);
        }

        return response()->json($equipe);
    }
}
