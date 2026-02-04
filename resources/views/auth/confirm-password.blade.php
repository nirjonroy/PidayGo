@extends('layouts.app')

@section('content')
    <h1>Confirm Password</h1>
    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autofocus>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror
        <button type="submit">Confirm</button>
    </form>
@endsection
