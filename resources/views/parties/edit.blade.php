@extends('adminlte::page')

@section('title', 'Editar Partido')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Partido</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-flag" title="Editar Partido" class="col-md-6">
    <form action="{{ route('parties.update', $party) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nombre --}}
        <x-adminlte-input name="name" label="Nombre" value="{{ old('name', $party->name) }}" required />
        @error('name')
            <x-adminlte-alert theme="danger" icon="fas fa-exclamation-circle">{{ $message }}</x-adminlte-alert>
        @enderror

        {{-- Foto actual y cambio de foto --}}
        <label for="foto_partido" class="form-label mt-3">Foto Actual</label>
        <div class="form-group">
            <div class="custom-file">
                <input type="file" name="foto_partido" id="foto_partido" class="custom-file-input @error('foto_partido') is-invalid @enderror" accept="image/*">
                <label class="custom-file-label" for="foto_partido">Seleccionar archivo</label>
                @error('foto_partido')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Vista previa de la foto --}}
        <div class="form-group">
            @if($party->foto_partido)
                <small class="form-text text-muted" id="preview-filename">Foto actual:</small>
                <div id="preview-container">
                    <img src="{{ asset('storage/' . $party->foto_partido) }}" alt="Foto actual" id="previewImg" 
                        style="max-width: 300px; max-height: 300px; border-radius: 5px;">
                </div>
            @else
                <div id="preview-container" style="display:none;">
                    <img src="#" alt="Vista previa" id="previewImg" 
                        style="max-width: 300px; max-height: 300px; border-radius: 5px;">
                </div>
            @endif
        </div>

        {{-- Botones --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('parties.index') }}" class="btn btn-default">Cancelar</a>
            <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop

@push('js')
<script>
    // Actualizar label y vista previa cuando se seleccione un archivo
    document.getElementById('foto_partido').addEventListener('change', function () {
        let file = this.files[0];
        let preview = document.getElementById('previewImg');
        let label = this.nextElementSibling; // label.custom-file-label

        if (file) {
            label.textContent = file.name;  // Cambia texto del label

            let reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            label.textContent = 'Seleccionar archivo';
            preview.src = '#';
            preview.style.display = 'none';
        }
    });
</script>
@endpush