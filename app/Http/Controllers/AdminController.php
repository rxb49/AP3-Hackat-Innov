<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Membre;
use App\Models\Inscrire;
use App\Utils\EmailHelpers;
use Illuminate\Http\Request;
use App\Utils\SessionHelpers;
use App\Models\Administrateur;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;


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
            return redirect("/adminlogin")->withErrors(['errors' => "Aucun administrateur n'a été trouvée avec cet email."]);
        }
        // Connexion de l'admin
        SessionHelpers::adminLogin($admin);
        // Redirection vers la page d'acceuil
        return redirect("/");
    }

    function listEquipe(){
        
        $equipe = Equipe::paginate(5);
        
        return view('admin.listEquipe', ['equipe' => $equipe]);
        }

    function download(Request $request){

        if ($request->has('idh')) {
            $idequipe = $request->input('idh');
        }

        // Récupère les données de l'équipe spécifique par ID
        $equipe = Equipe::find($idequipe);
        $inscription = Inscrire::where('idequipe', $idequipe)->get();
        $membre = Membre::where('idequipe', $idequipe)->get();

        $data = [
            'equipe' => $equipe,
            'inscription' => $inscription,
            'membre' => $membre
        ];

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        // Crée un nom de fichier pour le fichier JSON
        $fileName = "{$equipe->nomequipe}_data.json";

        // Définit le chemin temporaire pour le fichier
        $filePath = storage_path("app/public/$fileName");

        // Écrit le fichier JSON sur le disque
        File::put($filePath, $jsonData);

        $admin = SessionHelpers::getAdminConnected();

        EmailHelpers::sendEmail(
            $admin->email, 
            "Téléchargement des données de l'équipe {$equipe->nomequipe}", 
            "email.download-equipe", 
            ['admin' => $admin, 'equipe' => $equipe, 'inscription' => $inscription, 'membre' => $membre],
            $filePath // Passer le chemin du fichier JSON pour l'ajouter en pièce jointe
        );

        // Vérifie si l'équipe existe
        if (!$equipe) {
            return response()->json(['message' => 'Équipe non trouvée'], 404);
        }

        

        // Convertit les données de l'équipe en JSON
        $equipeJson = collect(['equipe' => $equipe, 'inscription' => $inscription, 'membre' => $membre])->toJson(JSON_PRETTY_PRINT);


        // Définit un nom pour le fichier temporaire
        $fileName = "equipe_{$idequipe}.json";
        $filePath = storage_path("app/public/$fileName");

        // Écrit les données JSON dans le fichier
        File::put($filePath, $equipeJson);

        // Retourne le fichier pour téléchargement et le supprime après l'envoi
        return response()->download($filePath, $fileName)->deleteFileAfterSend();

    }
}
