<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Administrateur extends Model
{
    use HasFactory;
    protected $table = 'ADMINISTRATEUR';
    protected $primaryKey = 'idadministrateur';
    public $timestamps = false;

    protected $fillable = ['nom', 'prenom', 'motpasse', 'email', 'google2fa_secret'];

    protected $hidden = [
    ];

    protected function google2faSecret(): Attribute

    {

        return new Attribute(

            get: fn ($value) =>  decrypt($value),

            set: fn ($value) =>  encrypt($value),

        );

    }

    public function enableA2F()
    {
        $this->is_a2f_enabled = true;
        $this->save();
    }

    // Désactiver l'A2F pour cet utilisateur
    public function disableA2F()
    {
        $this->is_a2f_enabled = false;
        $this->save();
    }

    // Vérifier si l'A2F est activée
    public function isA2FEnabled()
    {
        return $this->is_a2f_enabled;
    }
}
