@extends('layouts.app')

@section('title', 'Add pet')
@section('h1_title', 'Add pet')

@section('header')
    <a href="{{ route('pets.index') }}" class="button button-secondary">Back</a>
    <button type="submit" class="button button-primary" form="pet-form">Add</button>
@endsection

@section('content')

    <div id="message">
        @if ($errors->any())
            <p class="notification is-danger">{{ $errors->first() }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('pets.store') }}" id="pet-form">
        @csrf

        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>

        <select name="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </form>

@endsection
