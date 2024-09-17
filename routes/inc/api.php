<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

/**
 * Route relative aux API
 * Une API est un ensemble de routes qui permettent de récupérer des données en JSON
 *
 * L'objectif est de fournir des données aux applications mobiles, aux scripts et aux autres services
 * qui souhaitent interagir avec notre application.
 *
 * Exemple :
 *
 * - Application mobile qui affiche les hackathons en cours
 * - Ajax qui récupère les membres d'une équipe
 * - Bot discord qui affiche les équipes inscrites
 * - …
 */

// Route relative aux API HackathonController
Route::get('/api/hackathon/all', [ApiController::class, 'getAllHackathon']);
Route::get('/api/hackathon/active', [ApiController::class, 'getActiveHackathon']);
Route::get('/api/hackathon/{idh}/equipe', [ApiController::class, 'getEquipeByHackathon']);

// Route relative aux API membre
Route::get('/api/membre/all', [ApiController::class, 'getAllMembres']);
Route::get('/api/membre/{idequipe}', [ApiController::class, 'getByEquipeId']);

// Route relative aux API équipe
Route::get('/api/equipe/all', [ApiController::class, 'getAllEquipe']);
Route::post('/api/equipe/create', [ApiController::class, 'createEquipe']);
Route::post('/api/equipe/auth', [ApiController::class, 'authAuthEquipe']);
Route::get('/api/equipe/{token}', [ApiController::class, 'getByTokenEquipe']);
