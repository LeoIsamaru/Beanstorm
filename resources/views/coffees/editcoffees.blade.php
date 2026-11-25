@extends('layouts.fo')

@section('content')

    @if (session('message'))
        <div class="alert alert-success mt-3">
            {{ session('message') }}
        </div>
    @endif

    @php
        $isLoggedIn = Auth::check();
        $isAdmin = $isLoggedIn && Auth::user()->isAdmin();
        $isOwner = $isLoggedIn && (int)Auth::id() === (int)$coffeeInfo->user_id;
        $canEditCoffee = $isAdmin || $isOwner;
    @endphp

    <div class="row mt-4">
        {{-- Coluna esquerda: Detalhes do café --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Coffee Details: {{ $coffeeInfo->species }}
                    </h5>
                    @if ($canEditCoffee)
                        <span class="badge bg-light text-primary">{{ $isAdmin ? 'Admin Mode' : 'Owner Edit' }}</span>
                    @else
                        <span class="badge bg-secondary">Viewing Mode (Read-Only)</span>
                    @endif
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('coffees.update') }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{ $coffeeInfo->id }}">

                        <div class="mb-3">
                            <label class="form-label">Photo</label><br>
                            @if ($coffeeInfo->photo)
                                <img src="{{ asset('storage/' . $coffeeInfo->photo) }}" alt="Coffee Photo" class="img-thumbnail mb-2" width="150"><br>
                            @endif
                            @if ($canEditCoffee)
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            @endif
                        </div>
                        @error('photo')
                            <p class="text-danger">Invalid photo</p>
                        @enderror

                        <div class="mb-3">
                            <label for="species" class="form-label">Species</label>
                            <input value="{{ old('species', $coffeeInfo->species) }}" required type="text" name="species" id="species" class="form-control" {{ $canEditCoffee ? '' : 'disabled' }}>
                        </div>
                        @error('species')
                            <p class="text-danger">Invalid species</p>
                        @enderror

                        <div class="mb-3">
                            <label for="origin_country" class="form-label">Country of Origin</label>
                            <input value="{{ old('origin_country', $coffeeInfo->origin_country) }}" required type="text" name="origin_country" id="origin_country" class="form-control" {{ $canEditCoffee ? '' : 'disabled' }}>
                        </div>
                        @error('origin_country')
                            <p class="text-danger">Invalid country</p>
                        @enderror

                        <div class="mb-3">
                            <label for="roast_level" class="form-label">Roast Level</label>
                            <input value="{{ old('roast_level', $coffeeInfo->roast_level) }}" required type="text" name="roast_level" id="roast_level" class="form-control" {{ $canEditCoffee ? '' : 'disabled' }}>
                        </div>
                        @error('roast_level')
                            <p class="text-danger">Invalid roast level</p>
                        @enderror

                        @if ($coffeeInfo->user_name)
                            <p class="text-muted small"><strong>Owner:</strong> {{ $coffeeInfo->user_name }}</p>
                        @endif

                        @if ($canEditCoffee)
                            <button type="submit" class="btn btn-primary">Update Coffee</button>
                        @endif
                        <a href="{{ route('coffees.all') }}" class="btn btn-secondary">Back to All Coffees</a>
                    </form>
                </div>
            </div>
        </div>

        {{-- Coluna Direita: Marcas associadas ao tipo de café da coluna esquerda --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Brands under {{ $coffeeInfo->species }}</h5>
                </div>
                <div class="card-body">
                    <h6>Registered Brands</h6>
                    @if ($brands->isEmpty())
                        <p class="text-muted">No brands found for this coffee bean yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Brand</th>
                                        <th>Batch</th>
                                        <th>Price</th>
                                        <th>Caffeine</th>
                                        @if ($isAdmin)
                                            <th class="text-center">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brands as $brand)
                                        <tr>
                                            <td><strong>{{ $brand->name }}</strong></td>
                                            <td>{{ $brand->batch }}</td>
                                            <td>{{ number_format($brand->price, 2) }} €</td>
                                            <td>{{ $brand->caffeine }}</td>
                                            @if ($isAdmin)
                                                <td class="text-center">
                                                    <a href="{{ route('brands.delete', $brand->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this brand?')">Delete</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <hr>

                    {{-- Formulario para Add New Brand apenas visivel para Admins --}}
                    @if ($isAdmin)
                        <h6>Add New Brand to {{ $coffeeInfo->species }}</h6>
                        <form method="POST" action="{{ route('brands.store') }}">
                            @csrf
                            <input type="hidden" name="coffee_id" value="{{ $coffeeInfo->id }}">

                            <div class="mb-3">
                                <label for="brand_name" class="form-label">Brand Name</label>
                                <input type="text" name="name" id="brand_name" class="form-control" placeholder="e.g. Delta, Nespresso, Lavazza" required>
                            </div>

                            <div class="mb-3">
                                <label for="brand_batch" class="form-label">Batch</label>
                                <input type="text" name="batch" id="brand_batch" class="form-control" placeholder="e.g. Batch #2026-A" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_price" class="form-label">Price (€)</label>
                                    <input type="number" step="0.01" name="price" id="brand_price" class="form-control" placeholder="e.g. 5.99" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="brand_caffeine" class="form-label">Caffeine</label>
                                    <input type="text" name="caffeine" id="brand_caffeine" class="form-control" placeholder="e.g. 2.5%, Medium" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Add Brand</button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">Only administrators can add or remove brands.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
