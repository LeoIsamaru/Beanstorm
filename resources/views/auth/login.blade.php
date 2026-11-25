@extends('layouts.fo')

@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Login</h4>
                </div>
                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input required type="email" name="email" class="form-control" id="email"
                                value="{{ old('email') }}" aria-describedby="emailHelp">
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input required type="password" name="password" class="form-control" id="password">
                        </div>
                        @error('password')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        <p class="mt-3 text-center">Forgot your password? Click <a href="{{ route('password.request') }}">here</a></p>
                        <hr>
                        <p class="text-center mb-0">Don't have an account? <a href="{{ route('users.add') }}">Register here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
