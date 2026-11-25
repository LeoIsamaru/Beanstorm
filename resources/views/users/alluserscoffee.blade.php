@extends('layouts.fo')

@section('content')

    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <h1>Coffee Enthusiasts</h1>
        @if (Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('users.add') }}" class="btn btn-success">+ Add User</a>
        @endif
    </div>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form action="" method="GET">
        <div class="input-group mb-3" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ request('search') }}">
            <button class="btn btn-secondary" type="submit">Search</button>
        </div>
    </form>

    @php
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
    @endphp

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                {{-- ID e User Type apenas visiveis para Admins --}}
                @if ($isAdmin)
                    <th scope="col" class="text-center" style="width: 70px;">ID</th>
                    <th scope="col" class="text-center" style="width: 100px;">User Type</th>
                @endif

                <th scope="col" class="text-center">Name</th>

                {{-- Email apenas visivel para Admins --}}
                @if ($isAdmin)
                    <th scope="col" class="text-center">Email</th>
                @endif

                <th scope="col" class="text-center">Favorite Coffee (Brand + Batch)</th>
                <th scope="col" class="text-center">Disliked Coffee (Brand + Batch)</th>

                <th scope="col" class="text-center" style="width: 160px;">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usersFromDB as $user)
                @php
                    $isSelf = (int)Auth::id() === (int)$user->id;
                    $canEditUser = $isAdmin || $isSelf;
                @endphp
                <tr>
                    {{-- ID e User Type celulas (apenas Admins) --}}
                    @if ($isAdmin)
                        <th scope="row" class="text-center">{{ $user->id }}</th>
                        <td class="text-center">
                            @if ($user->user_type == \App\Models\User::TYPE_ADMIN)
                                <span class="badge bg-danger">Admin</span>
                            @else
                                <span class="badge bg-secondary">User</span>
                            @endif
                        </td>
                    @endif

                    <td class="text-center">
                        <strong>{{ $user->name }}</strong>
                        @if ($isSelf)
                            <span class="badge bg-primary ms-1">You</span>
                        @endif
                    </td>

                    {{-- Email cell (apenas Admins) --}}
                    @if ($isAdmin)
                        <td class="text-center">{{ $user->email }}</td>
                    @endif

                    {{-- Marca favorita (Name + Batch) --}}
                    <td class="text-center">
                        {{ $user->favorite_brand_display ?: ($user->favoriteBrand ? $user->favoriteBrand->name . ' - ' . $user->favoriteBrand->batch : '-') }}
                    </td>

                    {{-- Marca que não gosta (Name + Batch) --}}
                    <td class="text-center">
                        {{ $user->disliked_brand_display ?: ($user->dislikedBrand ? $user->dislikedBrand->name . ' - ' . $user->dislikedBrand->batch : '-') }}
                    </td>

                    <td class="text-center">
                        {{-- Admins podem editar qualquer user; user Normal apenas pode editar as suas escolhas --}}
                        @if ($canEditUser)
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm">
                                {{ $isSelf && !$isAdmin ? 'Edit My Picks' : 'Edit' }}
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif

                        {{-- Botão Delete visivel apenas para Admins --}}
                        @if ($isAdmin)
                            <a href="{{ route('users.delete', $user->id) }}" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
