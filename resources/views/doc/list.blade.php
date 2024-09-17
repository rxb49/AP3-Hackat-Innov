@extends('layouts.app')

@section('title', ' - Liste des API')

@section('content')
    <div class="documentation">
        <div class="card w-200">
            <div class="card-body">
                <h2 class="text-center">Documentation API (HTML)</h2>
                <div class="d-flex justify-content-center align-items-center">
                    <a class="btn btn-primary mx-3" href="/doc-api/hackathons">Les hackathons</a>
                    <a class="btn btn-primary mx-3" href="/doc-api/equipes">Les équipes</a>
                    <a class="btn btn-primary mx-3" href="/doc-api/membres">Les membres</a>
                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-body">
                <h2 class="text-center">Documentation API (OpenAPI)</h2>
                <div class="d-flex justify-content-center align-items-center">
                    <a class="btn btn-primary mx-3" href="/documentation.yaml" target="_blank">1. Copier la documentation YAML</a>
                    <a class="btn btn-primary mx-3" href="https://editor.swagger.io" target="_blank">2. Coller dans Swagger Editor</a>
                </div>
            </div>
        </div>
    </div>
@endsection
