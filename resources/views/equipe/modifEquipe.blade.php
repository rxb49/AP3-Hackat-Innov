@extends('layouts.app')

@section("title", " - Créer une équipe")

@section("content")
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg fullContainer">
        <div class="card cardRadius">
            <div class="card-body">
                <h3>Modifier votre Equipe</h3>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-unstyled text-start m-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/modif-team" method="post">
                    <!--
                    CSRF Token,
                    Le CSRF Token est une protection contre les attaques CSRF (Cross-Site Request Forgery).
                    Il est obligatoire de l'ajouter dans les formulaires de votre application Laravel.
                    Sinon, vous aurez une erreur de type 419.
                     -->
                    @csrf

                    <p>Information de votre équipe</p>
                    <input required type="text" class="form-control my-3" placeholder="Nom de votre équipe" name="nom" value="{{$equipe->nomequipe}}"/>
                    <input required type="text" class="form-control my-3" placeholder="Lien de votre site" name="lien" value="{{$equipe->lienprototype}}"/>

                    <hr/>
                    <p>Vos informations de connexion</p>
                    <input required type="email" class="form-control my-3" placeholder="Email" name="email" value="{{$equipe->login}}"/>
                    <input type="password" class="form-control my-3" name="password" placeholder="Nouveau mot de passe">
                    <input type="password" class="form-control my-3" name="password_confirmation" placeholder="Confirmer le mot de passe">


                    <hr/>
                    <input type="submit" value="Modifier mon équipe" class="btn btn-success">

                </form>
            </div>
        </div>
    </div>
@endsection
