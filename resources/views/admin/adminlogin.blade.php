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
        
        <!-- Le champ code_2fa n'est affiché que si nécessaire -->
        <div class="form-group fadeIn third" id="code2faGroup" style="margin: 15px;">
            <input type="text" 
                   class="form-control" 
                   name="code_2fa" 
                   placeholder="Code 2FA (si activé)" 
                   value="{{ old('code_2fa') }}"
                   pattern="[0-9]*"
                   inputmode="numeric"
                   minlength="6"
                   maxlength="6"/>
            <small class="form-text text-muted">Ne remplissez ce champ que si vous avez activé l'authentification à deux facteurs</small>
        </div>

        <input type="submit" class="fadeIn fourth" value="Connexion"/>
    </form>
</div>
@endsection
