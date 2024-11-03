<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hackathon extends Model
{
    use HasFactory;

    protected $table = 'HACKATHON';
    protected $primaryKey = 'idhackathon';
    public $timestamps = false;

    protected $fillable = ['dateheuredebuth', 'dateheurefinh', 'lieu', 'ville', 'conditions', 'thematique', 'affiche', 'objectifs', 'idorganisateur'];

    /*
     * Retourne le premier hackathon actif
     * Un hackathon est actif si sa date de fin est postérieure à la date actuelle
     * @return HackathonController
     */
    public static function getActiveHackathon(): Hackathon
    {
        return Hackathon::where('dateheurefinh', '>', now())->orderBy('dateheuredebuth')->first();
    }

    public function organisateur()
    {
        return $this->belongsTo(Organisateur::class, 'idorganisateur');
    }

    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'INSCRIRE', 'idhackathon', 'idequipe')->withPivot('dateinscription');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'idhackathon', 'idhackathon');
    }
}
