@extends('layouts.app')

@section('title', 'Add pet')
@section('h1_title', 'Add pet')

@section('header')
    <div class="buttons">
        <a href="{{ route('pets.index') }}" class="button button-secondary">Back</a>
        <button type="submit" class="button button-primary" form="pet-form">Add</button>
    </div>
@endsection

@section('content')

    <div id="message">
        @if ($errors->any())
            <p class="notification is-danger">{{ $errors->first() }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('pets.store') }}" id="pet-form">
        @csrf

        <div class="form-row">
            <label for="form-name">Name:</label>
            <input id="form-name" type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
        </div>

        <div class="form-row">
            <label for="form-status">Status:</label>
            <select id="form-status" name="status">
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

@endsection
