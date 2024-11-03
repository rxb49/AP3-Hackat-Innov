<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'COMMENTAIRE';
    protected $primaryKey = 'idcommentaire';

    // Vu que la clé primaire est composée de deux colonnes, on doit spécifier que la clé primaire n'est pas auto-incrémentée
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['idhackathon', 'libelle'];

    public function hackathon()
    {
        return $this->belongsTo(Hackathon::class, 'idhackathon', 'idhackathon');
    }

}
