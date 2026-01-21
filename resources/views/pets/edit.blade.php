@extends('layouts.app')

@section('title', 'Edit pet - #' . $pet['id'] . ' ' . $pet['name'])
@section('h1_title', 'Edit pet - #' . $pet['id'] . ' ' . $pet['name'])

@section('header')
    <div class="buttons">
        <a href="{{ route('pets.index') }}" class="button button-secondary">Back</a>
        <button type="submit" class="button button-red" form="delete-pet-form">Delete</button>
        <button type="submit" class="button button-primary" form="pet-form">Save</button>
    </div>
@endsection

@section('content')

    <div id="message">
        @if (session('success'))
            <p class="notification is-success">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <p class="notification is-danger">{{ $errors->first() }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('pets.update', $pet['id']) }}" id="pet-form">
        @csrf

        <div class="form-row">
            <label for="form-name">Name:</label>
            <input id="form-name" type="text" name="name" placeholder="Name" value="{{ $pet['name'] }}" required>
        </div>

        <div class="form-row">
            <label for="form-status">Status:</label>
            <select id="form-status" name="status">
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($pet['status'] === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

    </form>

    <form method="POST" action="{{ route('pets.destroy', $pet['id']) }}" onsubmit="return confirm('Delete this pet?')"
        id="delete-pet-form">
        @csrf
    </form>

@endsection
