@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Configuration de l'authentification à deux facteurs</div>

                <div class="card-body">
                    <p>1. Scannez ce QR code avec Google Authenticator :</p>
                    <div class="text-center">
                        <img src="https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl={{ urlencode($qrCodeUrl) }}">
                    </div>

                    <p class="mt-3">Ou entrez cette clé manuellement : {{ $secret }}</p>

                    <form method="POST" action="{{ route('admin.2fa.confirm') }}" class="mt-4">
                        @csrf
                        <div class="form-group">
                            <label>Entrez le code généré par Google Authenticator :</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary mt-3">Confirmer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection