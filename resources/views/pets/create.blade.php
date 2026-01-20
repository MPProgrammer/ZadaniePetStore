@extends('layouts.app')

@section('title', 'Dodaj zwierzę')
@section('h1_title', 'Dodaj zwierzę')

@section('header')
    <a href="{{ route('pets.index') }}" class="button button-secondary">Wstecz</a>
    <button type="submit" class="button button-primary" form="pet-form">Dodaj</button>
@endsection

@section('content')

    <div id="message">
        @if ($errors->any())
            <p style="color:red">{{ $errors->first() }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('pets.store') }}" id="pet-form">
        @csrf

        <input type="text" name="name" placeholder="Nazwa" value="{{ old('name') }}" required>

        <select name="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </form>

@endsection
