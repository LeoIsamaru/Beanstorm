@extends('layouts.fo')

@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        {{ Auth::check() && Auth::user()->isAdmin() ? 'Create New User' : 'Register' }}
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input required type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" placeholder="Full Name">
                        </div>
                        @error('name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input required type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="name@example.com" aria-describedby="emailHelp">
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input required type="password" name="password" class="form-control" id="password" placeholder="At least 8 characters">
                        </div>
                        @error('password')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        {{-- Dropdown para os Admin escolherem o User Type (predefinido: Normal User) --}}
                        @if (Auth::check() && Auth::user()->isAdmin())
                            <div class="mb-3">
                                <label for="user_type" class="form-label">User Type</label>
                                <select name="user_type" id="user_type" class="form-select">
                                    <option value="{{ \App\Models\User::TYPE_GENERIC_USER }}" {{ old('user_type', \App\Models\User::TYPE_GENERIC_USER) == \App\Models\User::TYPE_GENERIC_USER ? 'selected' : '' }}>
                                        Normal User (Default)
                                    </option>
                                    <option value="{{ \App\Models\User::TYPE_ADMIN }}" {{ old('user_type') == \App\Models\User::TYPE_ADMIN ? 'selected' : '' }}>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                            @error('user_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        @endif

                        <button type="submit" class="btn btn-primary">
                            {{ Auth::check() && Auth::user()->isAdmin() ? 'Create User' : 'Register' }}
                        </button>
                        <a href="{{ Auth::check() ? route('users.all') : route('login') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
