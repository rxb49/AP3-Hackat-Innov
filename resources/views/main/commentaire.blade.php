@extends('layouts.app')

@section('title', ' - Commentaires')

@section('custom-css')
    <link href="/css/home.css" rel="stylesheet"/>
    <style>
        .chat-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            position: relative;
            max-width: 80%;
            clear: both;
            border: 1px solid #e0e0e0; /* Bordure autour de chaque message */
            background-color: #fff; /* Arrière-plan par défaut pour les messages */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Légère ombre pour un effet d'élévation */
        }
        .message-time {
            font-size: 0.8em;
            color: #888;
            display: block; /* Reste en bloc pour garder la date sur une nouvelle ligne */
            margin-bottom: 5px; /* Ajoute un espace en dessous de la date */
        }
        .message-content {
            line-height: 1.4;
        }
        /* Styles pour les messages de l'utilisateur */
        .message.user {
            background-color: #dcf8c6; /* Couleur de la bulle de l'utilisateur */
            align-self: flex-end;
            margin-left: auto;
            border-color: #c2e1c2; /* Couleur de la bordure pour l'utilisateur */
        }
        /* Styles pour les messages des autres */
        .message.other {
            background-color: #fff; /* Couleur de la bulle des autres */
            align-self: flex-start;
            border-color: #e0e0e0; /* Couleur de la bordure pour les autres */
        }
        /* Conteneur pour aligner les messages comme un chat */
        .chat {
            display: flex;
            flex-direction: column;
        }

        /* Formulaire de commentaire */
        .comment-form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 10px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,.5);
            outline: none;
        }
        .btn {
            align-self: flex-start; /* Aligner le bouton à gauche */
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
            border: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3; /* Couleur au survol */
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            list-style-type: none;
            padding: 0;
        }
        .pagination li {
            margin: 0 5px;
        }
        .pagination a {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #007bff;
            color: #007bff;
            transition: background-color 0.3s;
        }
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }
        .pagination .active a {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
    </style>
@endsection

@section('content')
<div class="container mt-5">
<h1 class="text-center">Commentaires</h1>
<div class="chat-container">
    <div class="chat">
        @foreach ($data as $d)
            <div class="message {{ $d->is_user ? 'user' : 'other' }}">
                <div class="message-content">
                    <span class="message-time">{{ $d->created_at }}</span>
                    <p class="message-text">{{ htmlspecialchars($d->libelle) }}</p>
                </div>
            </div>
        @endforeach
    </div>

        <!-- Formulaire pour ajouter un commentaire -->
            <form action="/comment" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="idhackathon" value="{{ $hackathon->idhackathon }}">
                <div class="form-group">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Ajouter un commentaire..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
        <!-- Pagination -->
        <div class="pagination">
            {{ $data->appends(['idh' => $hackathon->idhackathon])->links('pagination::bootstrap-4') }}
        </div>
    
</div>
@endsection
