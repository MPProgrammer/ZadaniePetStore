@extends('layouts.app')

@section('title', 'Edytuj zwierzę - #' . $pet['id'] . ' ' . $pet['name'])
@section('h1_title', 'Edytuj zwierzę - #' . $pet['id'] . ' ' . $pet['name'])

@section('header')
    <a href="{{ route('pets.index') }}" class="button button-secondary">Wstecz</a>
    <button type="submit" class="button button-red" form="delete-pet-form">Usuń</button>
    <button type="submit" class="button button-primary" form="pet-form">Aktualizuj</button>
@endsection

@section('content')

    <div id="message">
        @if (session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <p style="color:red">{{ $errors->first() }}</p>
        @endif
    </div>

    <form method="POST" action="{{ route('pets.update', $pet['id']) }}" id="pet-form">
        @csrf

        <input type="text" name="name" value="{{ $pet['name'] }}" required>

        <select name="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected($pet['status'] === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </form>

    <form method="POST" action="{{ route('pets.destroy', $pet['id']) }}" onsubmit="return confirm('Delete this pet?')"
        id="delete-pet-form">
        @csrf
    </form>

@endsection
