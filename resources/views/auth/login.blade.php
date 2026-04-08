@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Login Aplikasi</div>
                <div class="card-body">
                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </form>
                    <p class="text-center">Belum punya akun?</p>

                    <a href="{{ route('register') }}">
                        <button type="button" class="btn btn-warning w-100">Register</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
