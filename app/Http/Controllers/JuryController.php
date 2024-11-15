<?php

namespace App\Http\Controllers;

use App\Models\JURY;
use App\Models\JURYMEMBRE;
use Illuminate\Http\Request;
use App\Utils\SessionHelpers;

class JuryController extends Controller
{

    public function juryLogin()
    {
        return view('jury.jurylogin');
    }

    public function juryConnect(Request $request)
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
        // Récupération de l'admin avec l'email fourni
        $jury = JURYMEMBRE::where('email', $validated['email'])->first();
        // Si le jury n'existe pas, on redirige vers la page de connexion avec un message d'erreur
        if (!$jury) {
            return redirect("/jurylogin")->withErrors(['errors' => "Aucun jury n'a été trouvée avec cet email."]);
        }

        if (!password_verify($validated['password'], $jury->password)) {
            return redirect("/jurylogin")->withErrors(['errors' => "Aucun jury n'a été trouvée avec ce mdp."]);
        }
        // Connexion de l'admin
        SessionHelpers::juryLogin($jury);
        // Redirection vers la page d'acceuil
        return redirect("/");
    }
}
