@extends('layouts.app')
@section('content')
    <h2 class="text-center m-3">Register Peminjam</h2>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card text-dark">
                <div class="card-body">
                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Password" required class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password Confirmation</label>
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
