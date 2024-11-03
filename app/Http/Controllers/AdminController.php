<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\SessionHelpers;
use App\Models\Administrateur;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FALaravel\Google2FA;

class AdminController extends Controller
{
    public function adminLogin()
    {
        return view('admin.adminlogin');
    }

    public function adminConnect(Request $request)
    {
        // 1. Validation des champs
        $validated = $request->validate([
            'email' => 'required|email',
            'motpasse' => 'required',
        ], [
            'required' => 'Le champ :attribute est obligatoire.',
            'email' => 'Le champ :attribute doit être une adresse email valide.',
        ]);

        // 2. Récupération de l'admin
        $admin = Administrateur::where('email', $validated['email'])->first();

        // Log pour déboguer
        Log::info('Tentative de connexion', [
            'email' => $validated['email'],
            'admin_exists' => $admin ? 'oui' : 'non'
        ]);

        // Si l'admin n'existe pas
        if (!$admin) {
            Log::info('Administrateur non trouvé');
            return redirect("/adminlogin")->withErrors(['errors' => "Identifiants invalides"]);
        }

        // 3. Vérification du mot de passe
        // Essayons les deux méthodes de vérification possibles
        $passwordValid = false;

        // Méthode 1 : password_verify()
        if (password_verify($validated['motpasse'], $admin->motpasse)) {
            $passwordValid = true;
            Log::info('Mot de passe validé avec password_verify');
        }
        // Méthode 2 : Hash::check()
        else if (Hash::check($validated['motpasse'], $admin->motpasse)) {
            $passwordValid = true;
            Log::info('Mot de passe validé avec Hash::check');
        }

        Log::info('Vérification mot de passe', [
            'password_valid' => $passwordValid ? 'oui' : 'non',
            'password_hash_type' => gettype($admin->motpasse),
            'password_length' => strlen($admin->motpasse)
        ]);

        if (!$passwordValid) {
            return redirect("/adminlogin")->withErrors(['errors' => "Identifiants invalides"]);
        }

        // 4. Vérification 2FA si activé
        if ($admin->google2fa_enabled) {
            $request->validate([
                'code_2fa' => 'required|numeric|digits:6'
            ], [
                'code_2fa.required' => 'Le code 2FA est requis',
                'code_2fa.numeric' => 'Le code 2FA doit être numérique',
                'code_2fa.digits' => 'Le code 2FA doit faire 6 chiffres'
            ]);

            $google2fa = app('pragmarx.google2fa');
            
            $valid = $google2fa->verifyKey(
                $admin->google2fa_secret,
                $request->input('code_2fa')
            );

            if (!$valid) {
                return redirect("/adminlogin")->withErrors(['errors' => "Code d'authentification invalide"]);
            }
        }

        // 5. Connexion réussie
        Log::info('Connexion réussie pour ' . $admin->email);
        SessionHelpers::adminLogin($admin);
        return redirect("/");
    }

    public function enable2FA(Request $request)
    {
        $admin = SessionHelpers::getConnected();
        $google2fa = app('pragmarx.google2fa');

        // Générer une nouvelle clé secrète
        $secret = $google2fa->generateSecretKey();

        // Sauvegarder la clé secrète temporairement en session
        session(['2fa_secret' => $secret]);

        // Générer le QR code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            $secret
        );

        return view('admin.setup2fa', compact('qrCodeUrl', 'secret'));
    }

    public function confirm2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6'
        ], [
            'code.required' => 'Le code est requis',
            'code.numeric' => 'Le code doit être numérique',
            'code.digits' => 'Le code doit faire 6 chiffres'
        ]);

        $admin = SessionHelpers::getConnected();
        $secret = session('2fa_secret');
        
        $google2fa = app('pragmarx.google2fa');
        
        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $admin->google2fa_secret = $secret;
            $admin->google2fa_enabled = true;
            $admin->save();
            
            session()->forget('2fa_secret');
            return redirect('/admin/profile')->with('success', '2FA activé avec succès');
        }

        return back()->withErrors(['code' => 'Code invalide']);
    }
}
