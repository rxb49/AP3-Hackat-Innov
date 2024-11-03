<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use Illuminate\Http\Request;

class CommentaireController extends Controller
{
    public function addCommentaire(Request $request)
    {
        // Validation du commentaire
        $request->validate([
            'comment' => 'required|string|max:255',
            'idhackathon' => 'required|integer',
        ]);

        // Création d'un nouveau commentaire
        Commentaire::create([
            'libelle' => $request->get('comment'),
            'idhackathon' => $request->get('idhackathon'),
        ]);

        return redirect()->back(); // Redirigez vers la même page
    }
}
