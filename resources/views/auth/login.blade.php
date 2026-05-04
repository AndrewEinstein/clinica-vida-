@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center p-3" style="background:#eef4f7;">
    <div class="panel p-4 p-md-5" style="max-width: 430px; width: 100%;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-mark text-white"><i class="bi bi-heart-pulse-fill"></i></div>
            <div>
                <h1 class="h4 mb-0">Clinica Vida+</h1>
                <div class="text-muted">Acesso ao sistema</div>
            </div>
        </div>
        <form method="POST" action="{{ route('login.store') }}" class="vstack gap-3">
            @csrf
            <div>
                <label class="form-label">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Manter conectado</label>
            </div>
            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar</button>
        </form>
        <div class="alert alert-light border mt-4 mb-0 small">
            <strong>Acesso demo:</strong> admin@clinicavida.test / password
        </div>
    </div>
</div>
@endsection
