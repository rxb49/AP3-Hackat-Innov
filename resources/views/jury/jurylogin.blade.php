@extends('layouts.admin')

@section('title', ' - Login')

@section('custom-css')
    <link href="/css/login.css" rel="stylesheet"/>
@endsection

@section('content')
<div class="wrapper fadeInDown">
    <form action="/jurylogin" method="post" id="formContent">
        @csrf
        <div class="fadeIn first">
            <img src="/img/user.png" class="icon" id="icon" alt="User Icon"/>
        </div>
        <h2>Login as Jury</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger mx-3 first">
                <ul class="list-unstyled m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <input type="text" id="email" class="fadeIn second" name="email" placeholder="Email" value="{{ old('email') }}"/>
        <input type="password" id="password" class="fadeIn third" name="password" placeholder="Mot de passe"/>

        <input type="submit" class="fadeIn fourth" value="Connexion"/>
    </form>
</div>
@endsection
