@extends('layouts.admin')

@section('title', 'Activer A2F')

@section('content')
    <h2>Activez la double authentification</h2>
    <p>Scannez ce QR Code avec votre application Google Authenticator :</p>
    <img src="{{ $qrCodeUrl }}" alt="QR Code Google Authenticator">

    <p>Une fois scanné, saisissez le code généré lors de votre prochaine connexion.</p>
@endsection
