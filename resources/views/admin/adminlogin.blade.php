@extends('layouts.admin')

@section('title', ' - Login')

@section('custom-css')
    <link href="/css/login.css" rel="stylesheet"/>
@endsection

@section('content')
<div class="wrapper fadeInDown">
    <form action="/adminlogin" method="post" id="formContent">
        @csrf
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

        <input type="text" id="login" class="fadeIn second" name="email" placeholder="Email" value="{{ old('email') }}"/>
        <input type="password" id="motpasse" class="fadeIn third" name="motpasse" placeholder="Mot de passe"/>
        <input type="text" id="otp" class="fadeIn third" name="otp" placeholder="Code de vérification (OTP)">

        <input type="submit" class="fadeIn fourth" value="Connexion"/>
    </form>
</div>
@endsection
