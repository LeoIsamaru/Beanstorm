@extends('layouts.fo')

@section('content')

<h1><a href="{{ route('home') }}">Back to Home!</a></h1>
<p></p>

<img src="{{ asset('images/cats.jpg') }}" alt="Coffee Cats" class="img-fluid rounded mb-3">

<p></p>

    @auth
        <h6>Hello, {{ Auth::user()->name }}!</h6>

        {{-- Message for admin / regular user --}}
        <div class="alert alert-success">
            @if (Auth::user()->isAdmin())
                Caution: Maximum Beanergy!!     {{-- Message for admin --}}
            @else
                Stay Grounded, Stay Awesome!    {{-- Message for regular user --}}
            @endif
        </div>
    @endauth

    <h5>Our App Features</h5>

    <ul>
        <li><a href="{{ route('coffees.all') }}">All Coffees</a></li>

        @auth
            {{-- Add Coffees visible only to Admin --}}
            @if (Auth::user()->isAdmin())
                <li><a href="{{ route('coffees.add') }}">Add Coffees</a></li>
                <li><a href="{{ route('users.all') }}">Users</a></li>
                <li><a href="{{ route('users.add') }}">Add Users</a></li>
            @else
                <li><a href="{{ route('users.all') }}">Users</a></li>
            @endif
        @endauth
    </ul>
@endsection
