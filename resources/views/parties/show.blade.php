@extends('adminlte::page')

@section('title', 'Detalle del Partido')

@section('content_header')
    <h1 class="m-0 text-dark">Detalle del Partido</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-flag" title="Partido: {{ $party->name }}" class="col-md-6">
    <p><strong>Nombre:</strong> {{ $party->name }}</p>
    <a href="{{ route('parties.index') }}" class="btn btn-default">Volver</a>
</x-adminlte-card>
@stop