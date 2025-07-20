@extends('adminlte::page')

@section('title', 'Asignar Roles')

@section('content_header')
    <h1 class="m-0 text-dark">Gestionar Roles: {{ $user->name }}</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-user-shield" title="Asignar Roles a {{ $user->name }}" class="col-md-8">
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')

        <x-adminlte-select2 name="roles[]" label="Roles"
                            igroup-size="md" fgroup-class="mb-3"
                            data-placeholder="Selecciona roles" :config="['multiple' => true]">
            @foreach($roles as $id => $name)
                <option value="{{ $id }}" @selected($user->roles->pluck('id')->contains($id))>
                    {{ $name }}
                </option>
            @endforeach
        </x-adminlte-select2>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop
