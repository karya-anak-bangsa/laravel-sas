@extends('layouts.admin-tamu')

@section('judul', 'Masuk')

@section('konten')
    <div class="auth-title">Masuk</div>
    <div class="auth-subtitle">Gunakan akun yang diberikan oleh Administrator sekolah.</div>

    @if (session('status'))
        <div class="alert alert-success" role="status">
            <div class="alert-body">{{ session('status') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.masuk') }}" novalidate>
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                @class(['form-control', 'is-invalid' => $errors->has('email')])
                autocomplete="username" autocapitalize="none" autofocus required>
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password"
                @class(['form-control', 'is-invalid' => $errors->has('password')])
                autocomplete="current-password" required>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-actions">
            <label class="form-check">
                <input type="checkbox" name="ingat" value="1" @checked(old('ingat'))> Ingat saya
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;height:38px">
            Masuk
        </button>
    </form>

    <div class="auth-footer">
        Lupa password? Hubungi Administrator sekolah.
    </div>
@endsection
