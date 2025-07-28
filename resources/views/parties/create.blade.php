@extends('adminlte::page')

@section('title', 'Crear Partido')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Partido</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-flag" title="Nuevo Partido" class="col-md-6">
    <form action="{{ route('parties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del partido" required />

        {{-- Campo de subida de imagen --}}
        <div class="form-group">
            <label for="foto_partido">Foto del Partido</label>
            <div class="custom-file">
                <input type="file" name="foto_partido" id="foto_partido" class="custom-file-input" accept="image/*" />
                <label class="custom-file-label" for="foto_partido">Seleccionar archivo</label>
            </div>
            <small id="fileName" class="form-text text-muted"></small>
            <img id="previewImg" src="#" alt="Vista previa" class="img-thumbnail mt-2 d-none" style="max-width: 300px;" />
        </div>

        {{-- Botones alineados a izquierda y derecha --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('parties.index') }}" class="btn btn-default">Cancelar</a>
            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop

@push('js')
<script>
document.getElementById('foto_partido').addEventListener('change', function () {
    let file = this.files[0];
    let preview = document.getElementById('previewImg');
    let label = this.nextElementSibling; // label.custom-file-label
    let fileName = document.getElementById('fileName');

    if (file) {
        label.textContent = file.name;
        fileName.textContent = `Archivo seleccionado: ${file.name}`;
        let reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        label.textContent = 'Seleccionar archivo';
        fileName.textContent = '';
        preview.src = '#';
        preview.classList.add('d-none');
    }
});
</script>
@endpush
