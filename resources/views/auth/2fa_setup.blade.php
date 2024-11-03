@extends('layouts.admin')

@section('content')
    <h2>Configurer l'authentification à deux facteurs</h2>
    <p>Scannez ce code QR avec Google Authenticator :</p>
    <img src="{{ $QR_Image }}" alt="QR Code">
    <p>Une fois que vous avez scanné le code QR, vous pouvez entrer le code généré ci-dessous :</p>
    <form action="{{ route('2fa.verify') }}" method="POST">
        @csrf
        <input type="text" name="code" required placeholder="Entrez le code 2FA">
        <button type="submit">Vérifier</button>
    </form>
@endsection
