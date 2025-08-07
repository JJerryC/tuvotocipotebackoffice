@extends('adminlte::page')

@section('title', 'Asignar Roles')

@section('content_header')
    <h1 class="m-0 text-dark">Gestionar Roles: {{ $user->name }}</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-user-shield" title="Asignar Roles a {{ $user->name }}" class="col-md-8">
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Botón de seleccionar todos --}}
        <div class="form-group">
            <label>
                <input type="checkbox" id="selectAllRoles"> Marcar / Desmarcar todos los roles
            </label>
        </div>

        {{-- Checkboxes de roles --}}
        <div class="form-group">
            <label>Roles disponibles</label>
            <div class="row">
                @foreach($roles as $id => $name)
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="roles[]" id="role{{ $id }}" value="{{ $id }}"
                                   @checked($user->roles->pluck('id')->contains($id))>
                            <label class="form-check-label" for="role{{ $id }}">
                                {{ $name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop

@section('js')
<script>
    document.getElementById('selectAllRoles').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('input[name="roles[]"]:not(:disabled)').forEach(cb => {
            cb.checked = checked;
        });
    });
</script>
@stop