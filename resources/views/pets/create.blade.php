@extends('layouts.app')

@section('content')

<h2>Add pet</h2>

<form method="POST" action="{{ route('pets.store') }}">
    @csrf

    <!-- <input type="number" name="id" placeholder="ID" value="{{ old('id') }}" required> -->
    <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>

    <select name="status">
        @foreach($statuses as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>

    <button type="submit">Add</button>
    <a href="{{ route('pets.index') }}">Back</a>
</form>

@if($errors->any())
    <p style="color:red">{{ $errors->first() }}</p>
@endif

@endsection
