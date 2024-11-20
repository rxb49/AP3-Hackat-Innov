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
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FALaravel\Support\Authenticator;



class AdminController extends Controller
{
    public function adminLogin()
    {
        return view('admin.adminlogin');
    }

    public function generateGoogle2FASecret($admin)
    {
        $google2fa = new Google2FA();
        $admin->google2fa_secret = $google2fa->generateSecretKey();
        $admin->save();

        return $admin->google2fa_secret;
    }

    public function showQrCode()
    {
        $admin = SessionHelpers::getAdminConnected(); // Récupère l'admin connecté
        $google2fa = app('pragmarx.google2fa');

        // Génère l'URL du QR Code
        $qrCodeUrl = $google2fa->getQRCodeInline(
            'NomApplication',       // Nom de l'application
            $admin->email,          // Email de l'utilisateur
            $admin->google2fa_secret // Clé secrète
        );

        return view('admin.qrcode', ['qrCodeUrl' => $qrCodeUrl]);
    }

    public function toggleTwoFactor(Request $request)
    {
        $admin = SessionHelpers::getAdminConnected();

        if (!$admin->google2fa_secret) {
            $this->generateGoogle2FASecret($admin);
            return redirect('/qrcode')->with('success', 'A2F activée. Scannez le QR Code.');
        } else {
            $admin->google2fa_secret = null; // Désactiver l'A2F
            $admin->save();
            return redirect('/')->with('success', 'A2F désactivée.');
        }
    }

    public function adminConnect(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'motpasse' => 'required',
            'otp' => 'required|digits:6', // Champ obligatoire pour le code OTP
        ]);
    
        $admin = Administrateur::where('email', $validated['email'])->first();
    
        // Vérifiez l'email et le mot de passe
        if (!$admin || !password_verify($validated['motpasse'], $admin->motpasse)) {
            return redirect("/adminlogin")->withErrors(['errors' => "Email ou mot de passe incorrect."]);
        }
    
        // Vérifiez le code OTP avec Google2FA
        $authenticator = app(Authenticator::class)->boot($request);
    
        if (!$authenticator->verifyGoogle2FA($admin->google2fa_secret, $validated['otp'])) {
            return redirect("/adminlogin")->withErrors(['errors' => "Code de vérification incorrect."]);
        }
    
        // Connexion de l'admin
        SessionHelpers::adminLogin($admin);
    
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
