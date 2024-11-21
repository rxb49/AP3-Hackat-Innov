@extends('layouts.admin')

@section('title', ' - Paramètres A2F')

@section('content')
<div class="container mt-5">
    <h1 class="text-center">Paramètres A2F</h1>

    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Informations de l'administrateur</h5>
            <p><strong>Nom :</strong> {{ $admin->nom }}</p>
            <p><strong>Email :</strong> {{ $admin->email }}</p>

            <form action="{{ route('toggleA2F') }}" method="POST">
                @csrf
                <button type="submit" class="btn @if($admin->is_a2f_enabled) btn-danger @else btn-success @endif">
                    @if($admin->is_a2f_enabled)
                        Désactiver l'A2F
                    @else
                        Activer l'A2F
                    @endif
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
