{{-- resources/views/home.blade.php --}}
@extends('adminlte::page')

@section('title', 'Dashboard')             {{-- Título de la pestaña --}}

@section('content_header')                 {{-- Encabezado dentro de la página --}}
    <h1>Dashboard</h1>
@stop

@section('content')                        {{-- Contenido principal --}}
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <p>¡Hola Cipote!</p>
@stop
