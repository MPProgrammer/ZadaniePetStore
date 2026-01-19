@extends('layouts.app')

@section('content')

<h2>Edit pet #{{ $pet['id'] }}</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('pets.update', $pet['id']) }}">
    @csrf

    <input type="text" name="name" value="{{ $pet['name'] }}" required>

    <select name="status">
        @foreach($statuses as $value => $label)
            <option value="{{ $value }}" @selected($pet['status'] === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>

    <button type="submit">Update</button>
</form>

<form method="POST"
      action="{{ route('pets.destroy', $pet['id']) }}"
      onsubmit="return confirm('Delete this pet?')">
    @csrf
    <button type="submit">Delete</button>
</form>

<a href="{{ route('pets.index') }}">Back</a>

@if($errors->any())
    <p style="color:red">{{ $errors->first() }}</p>
@endif

@endsection
