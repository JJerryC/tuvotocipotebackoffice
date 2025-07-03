@extends('adminlte::page')

@section('title', 'Asignar Roles y Permisos')

@section('content_header')
    <h1 class="m-0 text-dark">Gestionar: {{ $user->name }}</h1>
@stop

@section('content')
<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf @method('PUT')
    <div class="row">
        {{-- ROLES --}}
        <x-adminlte-select2 name="roles[]" label="Roles"
                            igroup-size="md" fgroup-class="col-md-6"
                            data-placeholder="Selecciona roles" :config="['multiple' => true]">
            @foreach($roles as $id => $name)
                <option value="{{ $id }}" @selected($user->roles->pluck('id')->contains($id))>
                    {{ $name }}
                </option>
            @endforeach
        </x-adminlte-select2>

        {{-- PERMISOS --}}
        <x-adminlte-select2 name="permissions[]" label="Permisos individuales"
                            igroup-size="md" fgroup-class="col-md-6"
                            data-placeholder="Selecciona permisos" :config="['multiple' => true]">
            @foreach($permissions as $id => $name)
                <option value="{{ $id }}" @selected($user->permissions->pluck('id')->contains($id))>
                    {{ $name }}
                </option>
            @endforeach
        </x-adminlte-select2>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Guardar
        </button>
    </div>
</form>
@stop
