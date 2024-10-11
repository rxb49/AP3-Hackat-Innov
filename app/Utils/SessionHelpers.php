<?php

namespace App\Utils;

use App\Models\Administrateur;
use App\Models\Equipe;

class SessionHelpers
{
    static string $sessionKey = "LOGIN";

    /**
     * Connecte une équipe, c'est-à-dire stocke l'équipe dans la session
     * @param Equipe $equipe
     * @return void
     */
    static function login(Equipe $equipe): void
    {
        session()->put(self::$sessionKey, $equipe);
        session()->save();
    }

    static function adminLogin(Administrateur $admin): void
    {
        session()->put(self::$sessionKey, $admin);
        session()->put('is_admin', true);
        session()->save();
    }

    /**
     * Déconnecte une équipe, c'est-à-dire supprime l'équipe de la session
     * @return void
     */
    static function logout(): void
    {
        session()->forget(self::$sessionKey);
        session()->put('is_admin', false);
        session()->save();
    }

    /**
     * Retourne l'équipe connectée, ou null si aucune équipe n'est connectée
     * @return Equipe|null
     */
    static function getConnected(): ?Equipe
    {
        return session(self::$sessionKey, null);
    }

    /**
     * Vérifie si une équipe est connectée. Retourne true si une équipe est connectée, false sinon
     * @return bool
     */
    static function isConnected(): bool
    {
        return session()->has(self::$sessionKey);
    }

    public static function isAdmin(): bool
    {
        return session()->has('is_admin') && session()->get('is_admin') === true;
    }
    

    public static function flash(string $type, $message)
    {
        session()->flash($type, $message);
    }
}
