@extends('layouts.fo')

@section('content')
    <div class="card shadow-sm mt-4" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">User Details: {{ $userInfo->name }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update') }}">
                @method('PUT')
                @csrf
                <input type="hidden" name="id" value="{{ $userInfo->id }}">

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input value="{{ old('name', $userInfo->name) }}" required type="text" name="name" class="form-control" id="name">
                </div>
                @error('name')
                    <p class="text-danger">Invalid Name</p>
                @enderror

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input value="{{ $userInfo->email }}" disabled type="email" name="email" class="form-control" id="email">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                @error('email')
                    <p class="text-danger">Invalid email</p>
                @enderror

                <div class="mb-3">
                    <label for="favorite_brand_id" class="form-label">Favorite Brand (Name + Batch)</label>
                    <select name="favorite_brand_id" id="favorite_brand_id" class="form-select">
                        <option value="">None</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (old('favorite_brand_id', $userInfo->favorite_brand_id ?? $userInfo->favorite_coffee_id) == $brand->id) ? 'selected' : '' }}>
                                {{ $brand->name }} - {{ $brand->batch }} ({{ $brand->coffee->species ?? 'Bean #' . $brand->coffee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="disliked_brand_id" class="form-label">Disliked Brand (Name + Batch)</label>
                    <select name="disliked_brand_id" id="disliked_brand_id" class="form-select">
                        <option value="">None</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (old('disliked_brand_id', $userInfo->disliked_brand_id ?? $userInfo->disliked_coffee_id) == $brand->id) ? 'selected' : '' }}>
                                {{ $brand->name }} - {{ $brand->batch }} ({{ $brand->coffee->species ?? 'Bean #' . $brand->coffee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('users.all') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
