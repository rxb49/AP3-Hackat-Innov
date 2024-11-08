<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use Illuminate\Http\Request;
use App\Utils\SessionHelpers;
use App\Models\Administrateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;


class AdminController extends Controller
{
    public function adminLogin()
    {
        return view('admin.adminlogin');
    }

    public function adminConnect(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => 'required|email',
                'motpasse' => 'required',
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
                'email' => 'Le champ :attribute doit être une adresse email valide.',
            ],
            [
                'email' => 'email',
                'motpasse' => 'mot de passe',
            ]
        );
        // Récupération de l'admin avec l'email fourni
        $admin = Administrateur::where('email', $validated['email'])->first();
        // Si l'admin n'existe pas, on redirige vers la page de connexion avec un message d'erreur
        if (!$admin) {
            return redirect("/adminlogin")->withErrors(['errors' => "Aucun administrateur n'a été trouvée avec cet email."]);
        }
        // Si le mot de passe est incorrect, on redirige vers la page de connexion avec un message d'erreur
        // Le message d'erreur est volontairement vague pour des raisons de sécurité
        // En cas d'erreur, on ne doit pas donner d'informations sur l'existence ou non de l'email
        if (!password_verify($validated['motpasse'], $admin->motpasse)) {
            return redirect("/adminlogin")->withErrors(['errors' => "Aucun administrateur n'a été trouvée avec cer email."]);
        }
        // Connexion de l'admin
        SessionHelpers::adminLogin($admin);
        // Redirection vers la page d'acceuil
        return redirect("/");
    }

    function download(){

        // Récupère tous les utilisateurs de la base de données
        $equipe = Equipe::all();

        // Convertit les données en format JSON
        $equipeJson = $equipe->toJson(JSON_PRETTY_PRINT);
        
        // Spécifiez le chemin et le nom du fichier JSON
        $filePath = storage_path('public/equipe.json');

        // Enregistre le JSON dans un fichier
        File::put($filePath, $equipeJson);

        return response()->json(['message' => 'Données exportées avec succès !', 'path' => $filePath]);

    }
}
