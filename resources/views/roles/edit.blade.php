@extends('adminlte::page')

@section('title','Editar rol')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Rol: {{ $role->name }}</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<x-adminlte-card theme="primary" icon="fas fa-user-tag" title="Editar El Rol: {{ $role->name }}" class="col-md-8">
    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>
                <input type="checkbox" id="checkAll"> Marcar / Desmarcar todos
            </label>
        </div>

        <div class="form-group">
            <label>Permisos</label>
            @foreach($permissions as $group => $perms)
                @if($perms->count() > 0)  {{-- Mostrar solo si hay permisos visibles --}}
                    <h5 class="mt-3">{{ ucfirst($group) }}</h5>
                    <div class="row">
                        @foreach($perms as $perm)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[]" id="p{{ $perm->id }}" value="{{ $perm->id }}"
                                           @checked($role->permissions->pluck('id')->contains($perm->id))
                                           @if(in_array($perm->name, $adminFixed ?? [])) disabled @endif
                                    >
                                    <label class="form-check-label" for="p{{ $perm->id }}">{{ $perm->name }}</label>

                                    {{-- Input hidden para enviar permisos deshabilitados --}}
                                    @if(in_array($perm->name, $adminFixed ?? []))
                                        <input type="hidden" name="permissions[]" value="{{ $perm->id }}">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Guardar
            </button>
        </div>
    </form>
</x-adminlte-card>
@stop

@section('js')
<script>
    document.getElementById('checkAll').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('input.form-check-input[type="checkbox"]').forEach(cb => {
            if (!cb.disabled) {
                cb.checked = checked;
            }
        });
    });
</script>
@stop
