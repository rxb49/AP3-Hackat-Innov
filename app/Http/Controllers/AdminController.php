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

    public function a2fSettings()
    {
        $admin = SessionHelpers::getAdminConnected(); // Récupérer l'admin connecté
        return view('admin.a2f-settings', ['admin' => $admin]);
    }

    public function toggleA2F(Request $request)
    {
        $admin = SessionHelpers::getAdminConnected();

        // Basculer l'état d'activation de l'A2F
        $admin->is_a2f_enabled = !$admin->is_a2f_enabled;
        $admin->save();

        $status = $admin->is_a2f_enabled ? 'activée' : 'désactivée';

        return redirect()->route('a2fSettings')->with('status', "L'authentification à deux facteurs a été $status avec succès.");
    }


    public function adminConnect(Request $request)
    {
        // Validation des champs du formulaire
        $validated = $request->validate(
            [
                'email' => 'required|email',
                'motpasse' => 'required',
                'otp' => 'nullable|digits:6' // L'OTP est requis seulement si A2F est activée
            ],
            [
                'required' => 'Le champ :attribute est obligatoire.',
                'email' => 'Le champ :attribute doit être une adresse email valide.',
                'digits' => 'Le code OTP doit contenir 6 chiffres.'
            ],
            [
                'email' => 'email',
                'motpasse' => 'mot de passe',
                'otp' => 'code OTP'
            ]
        );

        // Récupération de l'admin avec l'email fourni
        $admin = Administrateur::where('email', $validated['email'])->first();

        // Si l'admin n'existe pas, retour avec une erreur
        if (!$admin) {
            return redirect("/adminlogin")->withErrors(['errors' => "Aucun administrateur n'a été trouvé avec cet email."]);
        }

        // Vérification du mot de passe
        if (!password_verify($validated['motpasse'], $admin->motpasse)) {
            return redirect("/adminlogin")->withErrors(['errors' => "Le mot de passe est incorrect."]);
        }

        // Si l'A2F est activée, vérifier le code OTP
        if ($admin->is_a2f_enabled) {
            // Vérifier si l'OTP a été soumis
            if (empty($validated['otp'])) {
                return redirect("/adminlogin")->withErrors(['errors' => "Le code OTP est requis pour se connecter."]);
            }

            // Vérification du code OTP
            $google2fa = app('pragmarx.google2fa');
            $isOtpValid = $google2fa->verifyKey($admin->google2fa_secret, $validated['otp']);

            if (!$isOtpValid) {
                return redirect("/adminlogin")->withErrors(['errors' => "Le code OTP est invalide."]);
            }
        }

        // Si tout est valide, connecter l'administrateur
        SessionHelpers::adminLogin($admin);

        // Redirection vers la page d'accueil
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
