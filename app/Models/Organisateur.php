<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisateur extends Model
{
    use HasFactory;

    protected $table = 'ORGANISATEUR';
    protected $primaryKey = 'idorganisateur';
    public $timestamps = false;

    protected $fillable = ['nom', 'prenom', 'email'];

    public function hackathons()
    {
        return $this->hasMany(Hackathon::class, 'idorganisateur');
    }
}
