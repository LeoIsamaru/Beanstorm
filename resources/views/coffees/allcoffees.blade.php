@extends('layouts.fo')

@section('content')

@if (session('message'))
    <div class="alert alert-success mt-3">
        {{ session('message') }}
    </div>
@endif

    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <h1>All Coffees</h1>
        {{-- Botão Add Coffee apenas visvel para os Admin --}}
        @if (Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('coffees.add') }}" class="btn btn-success">+ Add Coffee</a>
        @endif
    </div>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">Photo</th>
                <th scope="col" class="text-center">Species</th>
                <th scope="col" class="text-center">Country of Origin</th>
                <th scope="col" class="text-center">Roast Level</th>
                <th scope="col" class="text-center" style="width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getAllCoffees as $coffee)
                @php
                    $isLoggedIn = Auth::check();
                    $isAdmin = $isLoggedIn && Auth::user()->isAdmin();
                    $isOwner = $isLoggedIn && (int)Auth::id() === (int)$coffee->user_id;
                    $canEdit = $isAdmin || $isOwner;
                @endphp
                <tr>
                    <th scope="row" class="text-center">{{ $coffee->id }}</th>
                    <td class="text-center">
                        <img src="{{ $coffee->photo ? asset('storage/' . $coffee->photo) : asset('images/arabica.jpg') }}" alt="Coffee Photo" width="50" class="img-thumbnail">
                    </td>
                    <td class="text-center">{{ $coffee->species }}</td>
                    <td class="text-center">{{ $coffee->origin_country }}</td>
                    <td class="text-center">{{ $coffee->roast_level }}</td>
                    <td class="text-center">
                        {{-- If user can edit (Admin or Owner), show Edit; otherwise show View --}}
                        @if ($canEdit)
                            <a href="{{ route('coffees.edit', $coffee->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        @else
                            <a href="{{ route('coffees.edit', $coffee->id) }}" class="btn btn-info btn-sm">View</a>
                        @endif

                        {{-- Botão Delete apenas visvel para os Admin --}}
                        @if ($isAdmin)
                            <a href="{{ route('coffees.delete', $coffee->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this coffee?')">Delete</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
