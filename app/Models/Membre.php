<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membre extends Model
{
    use HasFactory;

    protected $table = 'MEMBRE';
    protected $primaryKey = 'idmembre';
    public $timestamps = false;

    protected $fillable = ['idequipe', 'nom', 'prenom', 'email', 'telephone', 'datenaissance', 'lienportfolio'];

    public function equipe()
    {
        return $this->belongsTo(Equipe::class, 'idequipe');
    }
}
