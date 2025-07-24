@extends('adminlte::page')

@section('title', 'Detalle del Candidato')

@push('css')
    <style>
        .candidate-show-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 2rem;
            color: #fff;
        }

        .candidate-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .candidate-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid var(--accent-blue);
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        .candidate-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-card h5 {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.2rem;
        }

        .info-card p {
            font-weight: 600;
            font-size: 1rem;
        }

        /* Planilla block - aparte */
        .candidate-planilla {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1rem 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2rem;
            max-width: 350px;
            /* centrar */
            margin-left: auto;
            margin-right: auto;
        }

        .planilla-image {
            max-width: 100%;
            max-height: 150px;
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
            object-fit: contain;
            display: block;
        }
        .planilla-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-blue);
        }

        .candidate-propuestas {
            margin-top: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
            font-size: 1rem;
            color: #f1f1f1;
        }

        .candidate-propuestas h4 {
            margin-bottom: 1rem;
            color: var(--accent-blue);
        }

        .candidate-propuestas::-webkit-scrollbar {
            width: 8px;
        }

        .candidate-propuestas::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
        }

        .back-button {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            background: transparent;
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-button:hover {
            background: var(--primary-gradient);
            color: #fff;
            transform: translateY(-3px);
        }
    </style>
@endpush

@section('content')
<div class="candidate-show-container">
    <div class="candidate-header">
        <img 
            src="{{ $candidate->fotografia ? asset('storage/' . $candidate->fotografia) : asset('images/default-candidate.png') }}" 
            alt="Foto del candidato" 
            class="candidate-photo">
        <h2>{{ $candidate->primer_nombre }} {{ $candidate->segundo_nombre }} {{ $candidate->primer_apellido }} {{ $candidate->segundo_apellido }}</h2>
        <p class="text-secondary">
            @if($candidate->independiente)
                <span class="badge bg-warning text-dark">Independiente</span>
            @else
                {{ $candidate->party->name ?? 'Sin partido' }} – {{ $candidate->entidad->name ?? 'Sin entidad' }}
            @endif
        </p>
    </div>

    <div class="candidate-info">
        <div class="info-card">
            <h5>Número de Identidad</h5>
            <p>{{ $candidate->numero_identidad }}</p>
        </div>
        <div class="info-card">
            <h5>Departamento</h5>
            <p>{{ $candidate->departamento->name ?? 'Sin asignar' }}</p>
        </div>
        <div class="info-card">
            <h5>Municipio</h5>
            <p>{{ $candidate->municipio->name ?? 'Sin asignar' }}</p>
        </div>
        <div class="info-card">
            <h5>Cargo</h5>
            <p>{{ $candidate->cargo->name ?? 'Sin cargo' }}</p>
        </div>
        <div class="info-card">
            <h5>Sexo</h5>
            <p>{{ $candidate->sexo->description ?? 'No definido' }}</p>
        </div>
        <div class="info-card">
            <h5>Nómina</h5>
            <p>{{ $candidate->nomina->name ?? 'N/A' }}</p>
        </div>
        <div class="info-card">
            <h5>Posición</h5>
            <p>{{ $candidate->posicion }}</p>
        </div>
        <div class="info-card">
            <h5>¿Reelección?</h5>
            <p>{{ $candidate->reeleccion ? 'Sí' : 'No' }}</p>
        </div>
    </div>

    {{-- Bloque destacado para la planilla --}}
@if($candidate->planilla)
    <div class="candidate-planilla" style="display: flex; flex-direction: column; align-items: center; margin-top: 1.5rem;">
        <div class="planilla-name" style="font-weight: bold; font-size: 1.2rem; color: var(--accent-blue); margin-bottom: 0.5rem; text-align: center;">
            {{ $candidate->planilla->nombre }}
        </div>
        @if($candidate->planilla->foto)
            <img 
                src="{{ asset('storage/' . $candidate->planilla->foto) }}" 
                alt="Foto de la planilla" 
                class="planilla-image" 
                style="max-height: 150px; object-fit: contain; border-radius: 10px; border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);">
        @endif
    </div>
@endif

    <div class="candidate-propuestas">
        <h4><i class="fas fa-lightbulb mr-1"></i> Planes y Propuestas</h4>
        <p>{!! nl2br(e($candidate->propuestas ?? 'Sin propuestas registradas.')) !!}</p>
    </div>

    <a href="{{ route('dashboard.candidatos') }}" class="back-button">
        <i class="fas fa-arrow-left mr-1"></i> Volver
    </a>
</div>
@endsection