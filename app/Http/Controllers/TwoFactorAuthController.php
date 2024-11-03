<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Facades\Google2FA;

class TwoFactorAuthController extends Controller
{
        public function setup2FA(Request $request)
        {
            $user = Auth::user();
            $user->google2fa_secret = Google2FA::generateSecretKey();
            $user->save();
    
            $QR_Image = Google2FA::getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );
    
            // Retourner l'image QR à afficher ou sauvegarder
            return view('auth.2fa_setup', ['QR_Image' => $QR_Image]);
        }
}
