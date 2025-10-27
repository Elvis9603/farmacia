@extends('layouts.principal')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Control de Vencimientos</h1>
        <livewire:alertas-vencimiento />
    </div>
@endsection
