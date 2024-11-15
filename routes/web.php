<?php

use App\Models\Equipe;
use App\Utils\SessionHelpers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiDocController;
use App\Http\Controllers\EquipeController;
use App\Http\Middleware\IsEquipeConnected;
use App\Http\Controllers\HackathonController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\JuryController;

include('inc/api.php');

// Routes de base
Route::get('/', [MainController::class, 'home'])->name('home');
Route::get('/about', [MainController::class, 'about'])->name('about');
Route::get('/equipes/detailEquipe', [EquipeController::class, 'detailEquipe'])->name('detail-equipe');
Route::get('/archive', [HackathonController::class, 'list'])->name('archive');
Route::get('/passedArchive', [HackathonController::class, 'listPassedHackathon'])->name('passedhackathon');
Route::get('/incomingArchive', [HackathonController::class, 'listIncomingHackathon'])->name('incominghackathon');
Route::get('/archiveByEquipe', [HackathonController::class, 'listHackathonByEquipe'])->name('archiveByEquipe');
Route::get('/archive/commentaire', [HackathonController::class, 'commentaire'])->name('commentaire');
Route::post('/comment', [CommentaireController::class, 'addCommentaire'])->name('addCommentaire');




// Routes d'authentification et de gestion d'équipe
Route::get('/login', [EquipeController::class, 'login'])->name('login');
Route::post('/login', [EquipeController::class, 'connect'])->name('connect');
Route::get('/join', [HackathonController::class, 'join'])->name('join');
Route::post('/quit', [HackathonController::class, 'quit'])->name('quit');
Route::any('/create-team', [EquipeController::class, 'create'])->name('create-team'); // Any pour gérer les GET et POST
Route::any('/modif-team', [EquipeController::class, 'modif'])->name('modif-team');
Route::get('/adminlogin', [AdminController::class, 'adminLogin'])->name('adminlogin');
Route::post('/adminlogin', [AdminController::class, 'adminConnect'])->name('adminConnect');
Route::get('/jurylogin', [JuryController::class, 'juryLogin'])->name('jurylogin');
Route::post('/jurylogin', [JuryController::class, 'juryConnect'])->name('juryConnect');
Route::get('/listequipe', [AdminController::class, 'listEquipe'])->name('listequipe');
Route::get('/download', [AdminController::class, 'download'])->name('download');





// Routes de l'API pour la documentation et les listes
Route::get('/doc-api/', [ApiDocController::class, 'liste'])->name('doc-api');
Route::get('/doc-api/hackathons', [ApiDocController::class, 'listeHackathons'])->name('doc-api-hackathons');
Route::get('/doc-api/membres', [ApiDocController::class, 'listeMembres'])->name('doc-api-membres');
Route::get('/doc-api/equipes', [ApiDocController::class, 'listeEquipes'])->name('doc-api-equipes');

// Routes protégées nécessitant une session active, pour les équipes.
// Proctection par le middleware IsEquipeConnected (voir app/Http/Middleware/IsEquipeConnected.php)
Route::middleware(isEquipeConnected::class)->group(function () {
    Route::get('/logout', [EquipeController::class, 'logout'])->name('logout');
    Route::get('/modifEquipe', [EquipeController::class, 'modifEquipe'])->name('modifEquipe');
    Route::get('/me', [EquipeController::class, 'me'])->name('me');
    Route::post('/me', [EquipeController::class, 'addMembre'])->name('addMembre');
    Route::post('/membre/add', [EquipeController::class, 'addMembre'])->name('membre-add');
    Route::delete('/membre/{id}', [EquipeController::class, 'deleteMembre'])->name('deleteMembre');


});
