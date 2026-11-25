@extends('layouts.fo')

@section('content')
    <h1>Add a Coffee Bean</h1>
    <form method="POST" action="{{ route('coffees.store') }}" enctype="multipart/form-data">
        @csrf


        <div class="mb-3"> {{-- atribui o novo café há classe dos admins --}}
            <label for="assigned_to" class="form-label">Assigned To</label>
            <input type="text" id="assigned_to" class="form-control" value="{{ $adminName }}" disabled>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
        </div>
        @error('photo')
            <p class="text-danger">Invalid photo</p>
        @enderror

        <div class="mb-3">
            <label for="species" class="form-label">Species / Name</label>
            <input required type="text" name="species" class="form-control" id="species" value="{{ old('species') }}" placeholder="e.g. Arabica, Robusta, Liberica">
        </div>
        @error('species')
            <p class="text-danger">Invalid species</p>
        @enderror

        <div class="mb-3">
            <label for="origin_country" class="form-label">Country of Origin</label>
            <input required type="text" name="origin_country" class="form-control" id="origin_country" value="{{ old('origin_country') }}" placeholder="e.g. Ethiopia, Brazil, Colombia">
        </div>
        @error('origin_country')
            <p class="text-danger">Invalid country</p>
        @enderror

        <div class="mb-3">
            <label for="roast_level" class="form-label">Roast Level</label>
            <input required type="text" name="roast_level" class="form-control" id="roast_level" value="{{ old('roast_level') }}" placeholder="e.g. Light, Medium, Dark">
        </div>
        @error('roast_level')
            <p class="text-danger">Invalid roast level</p>
        @enderror


        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="{{ route('coffees.all') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
