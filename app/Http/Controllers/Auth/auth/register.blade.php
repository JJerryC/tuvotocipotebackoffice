@extends('adminlte::page')

@section('title', 'Registro de Usuario')

@push('css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-gradient, linear-gradient(135deg,#1a1a2e,#0f3460));
        }

        .dashboard-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
        }

        .register-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 20px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
        }

        .register-card h3 {
            font-weight: 700;
            font-size: 1.8rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-label, .form-check-label, .text-link {
            color: #fff;
        }

        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .form-control:focus {
            border-color: #00d4ff;
            box-shadow: 0 0 0 0.2rem rgba(0,212,255,0.25);
        }

        .btn-primary {
            background-color: #4F46E5;
            border: none;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #3730a3;
        }

        .alert {
            background-color: rgba(255,0,0,0.2);
            color: #fff;
            border: 1px solid rgba(255,0,0,0.3);
        }

        .invalid-feedback {
            color: #f87171;
        }

        .text-link a {
            color: #00d4ff;
            text-decoration: underline;
        }

        .logo {
            width: 90px;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">
        <div class="register-card">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo2.png') }}" alt="Logo Tu Voto Cipote" class="logo mb-2">
                <h3>Registro de Usuario</h3>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre completo</label>
                    <input id="name" type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Ej. Juan Pérez">

                    @error('name')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ej. correo@ejemplo.com">

                    @error('email')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password" placeholder="********">

                    @error('password')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label">Confirmar contraseña</label>
                    <input id="password-confirm" type="password"
                           class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="********">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Registrarme</button>
                </div>
            </form>

            <div class="text-link mt-3 text-center">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
            </div>
        </div>
    </div>
@endsection
