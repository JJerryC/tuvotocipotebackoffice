@extends('adminlte::page')

@section('title','Crear usuario')

@section('content_header')
    <h1 class="m-0 text-dark">Nuevo usuario</h1>
@stop

@section('content')
@if($errors->any())
  <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form action="{{ route('users.store') }}" method="POST">
    @csrf
    <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Datos</h3></div>

        <div class="card-body">
            <div class="row">
                {{-- Nombre --}}
                <div class="form-group col-md-6">
                    <label for="name">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="{{ old('name') }}">
                </div>

                {{-- Correo --}}
                <div class="form-group col-md-6">
                    <label for="email">Correo</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}">
                </div>

                {{-- Contraseña --}}
                <div class="form-group col-md-6">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                {{-- Confirmación --}}
                <div class="form-group col-md-6">
                    <label for="password_confirmation">Confirmar</label>
                    <input type="password" class="form-control"
                           id="password_confirmation" name="password_confirmation">
                </div>
            </div>

            <hr>

            <div class="row">
{{-- Roles --}}
<div class="form-group col-md-6">
    <label>Roles</label>

    {{-- Checkbox maestro --}}
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="checkAllRoles">
        <label class="form-check-label font-weight-bold" for="checkAllRoles">
            Marcar / Desmarcar todos los roles
        </label>
    </div>

    {{-- Checkboxes individuales --}}
    @foreach($roles as $id => $name)
        <div class="form-check">
            <input class="form-check-input" type="checkbox"
                   name="roles[]" value="{{ $id }}" id="r{{ $id }}">
            <label class="form-check-label" for="r{{ $id }}">{{ $name }}</label>
        </div>
    @endforeach
</div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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
    document.getElementById('checkAllRoles').addEventListener('change', function () {
        const isChecked = this.checked;
        document.querySelectorAll('input[name="roles[]"]:not(:disabled)').forEach(cb => {
            cb.checked = isChecked;
        });
    });
</script>
@stop
