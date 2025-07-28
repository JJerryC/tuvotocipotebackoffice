@extends('adminlte::page')

@section('title','Crear rol')

@section('content_header')
    <h1 class="m-0 text-dark">Nuevo rol</h1>
@stop

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('roles.store') }}">
    @csrf
    <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Datos</h3></div>

        <div class="card-body">
            {{-- nombre --}}
            <div class="form-group">
                <label for="role_name">Nombre del rol</label>
                <input type="text" id="role_name" name="role_name"
                       class="form-control" value="{{ old('role_name') }}"
                       placeholder="ej: editor">
            </div>

            {{-- Checkbox para marcar todos --}}
            <div class="form-group">
                <label>
                    <input type="checkbox" id="checkAll"> Marcar / Desmarcar todos
                </label>
            </div>

            {{-- permisos --}}
            <div class="form-group">
                <label>Permisos</label>
                @foreach($permissions as $group => $perms)
                    @if($perms->count() > 0)
                        <h5 class="mt-3">{{ ucfirst($group) }}</h5>
                        <div class="row">
                            @foreach($perms as $perm)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" id="p{{ $perm->id }}" value="{{ $perm->id }}">
                                        <label class="form-check-label" for="p{{ $perm->id }}">{{ $perm->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Cancelar
            </a>
            <button class="btn btn-primary ml-auto">
                <i class="fas fa-save mr-1"></i> Guardar
            </button>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    document.getElementById('checkAll').addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
            cb.checked = checked;
        });
    });
</script>
@stop