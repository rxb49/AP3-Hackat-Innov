<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscrire extends Model
{
    use HasFactory;

    protected $table = 'INSCRIRE';
    protected $primaryKey = ['idhackathon', 'idequipe'];

    // Vu que la clé primaire est composée de deux colonnes, on doit spécifier que la clé primaire n'est pas auto-incrémentée
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['idhackathon', 'idequipe', 'dateinscription'];


    public static function getNbInscrit($idHackathon) : int
    {
        $nbInscrit = DB::table('INSCRIRE')
            ->where('idhackathon', $idHackathon)
            ->count('idequipe');  // On compte le nombre d'équipes inscrites
    
        return $nbInscrit;
    }
}
