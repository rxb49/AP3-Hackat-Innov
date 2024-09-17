@extends('layouts.app')

@section("title", " - Créer une équipe")

@section("content")
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg fullContainer">
        <div class="card cardRadius">
            <div class="card-body">
                <h3>Créer une nouvelle équipe</h3>
                <p>Vous souhaitez nous rejoindre ?</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-unstyled text-start m-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/create-team" method="post">
                    <!--
                    CSRF Token,
                    Le CSRF Token est une protection contre les attaques CSRF (Cross-Site Request Forgery).
                    Il est obligatoire de l'ajouter dans les formulaires de votre application Laravel.
                    Sinon, vous aurez une erreur de type 419.
                     -->
                    @csrf

                    <p>Information de votre équipe</p>
                    <input required type="text" class="form-control my-3" placeholder="Nom de votre équipe" name="nom"/>
                    <input required type="text" class="form-control my-3" placeholder="Lien de votre site" name="lien"/>

                    <hr/>
                    <p>Vos informations de connexion</p>
                    <input required type="email" class="form-control my-3" placeholder="Email" name="email"/>
                    <input required type="password" class="form-control my-3" placeholder="Mot de passe" name="password"/>

                    <hr/>
                    <input type="submit" value="Créer mon équipe" class="btn btn-success">

                </form>
            </div>
            <a href="{{ route("login") }}" class="fadeIn fourth d-block p-2 text-black">J'ai déjà un compte</a>
        </div>
    </div>
@endsection
