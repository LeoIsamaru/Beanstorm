@extends('layouts.fo')

@section('content')

<h1><a href="{{ route('home') }}">Voltar à Home!</a></h1>

    {{-- @auth --}}
        <h6>Olá {{Auth::user()->name}}!</h6>
    {{-- @endauth --}}


    {{-- update --}}
  @if(Auth::user()->user_type == \App\Models\User::TYPE_ADMIN)
    <div class="alert alert-success">
        tens super poderes    </div>


    @endif

@endsection
