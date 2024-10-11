@extends('layouts.admin')

@section('title', ' - Login')

@section('custom-css')
    <link href="/css/login.css" rel="stylesheet"/>
@endsection

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg fullContainer">

        <div class="wrapper fadeInDown">
            <form action="/adminlogin" method="post" id="formContent">
                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="/img/user.png" class="icon" id="icon" alt="User Icon"/>
                </div>
                <h2>Login as administrator</h2>
                @if ($errors->any())
                    <div class="alert alert-danger mx-3 first">
                        <ul class="list-unstyled m-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{route("adminConnect")}}">
                    @csrf
                    <input type="text" id="login" class="fadeIn second" name="email" placeholder="Email"/>
                    <input type="password" id="motpasse" class="fadeIn third" name="motpasse" placeholder="Mot de passe"/>
                    <input type="submit" class="fadeIn fourth" value="Connexion"/>
                </form>


            </form>
        </div>

    </div>
@endsection
