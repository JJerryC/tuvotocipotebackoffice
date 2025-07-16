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
    
    @can('view candidates')
    <a href="{{ route('candidates.index') }}">Ver candidatos</a>
@endcan

@can('create candidates')
    <a href="{{ route('candidates.create') }}">Crear candidato</a>
@endcan
@can('view users')
    <a href="{{ route('users.index') }}">Ver usuarios</a>
@endcan

@can('create users')
    <a href="{{ route('users.create') }}">Crear usuario</a>
@endcan

@can('edit users')
    <a href="{{ route('users.edit', $user->id) }}">Editar usuario</a>
@endcan

@if(auth()->user()->can('view users') || auth()->user()->can('edit users'))
    <li class="nav-item">
        <a href="{{ route('users.index') }}" class="nav-link">Usuarios</a>   {{-- Crear menú visible solo por permisos --}}
    </li>
@endif

@can('view partidos') {{-- Muestra solo lo que el usuario tiene permitido --}}
    <a href="{{ route('partidos.index') }}">Partidos</a>
@endcan

@can('edit usuarios')
    <a href="{{ route('users.edit', $user->id) }}">Editar usuario</a>
@endcan
@stop
