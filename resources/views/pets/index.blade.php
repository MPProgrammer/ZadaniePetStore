@extends('layouts.app')

@section('title', 'Lista petów')

@section('content')

<h2>Lista petów</h2>

<div id="message">
    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif
</div>

<a href="{{ route('pets.create') }}">Add pet</a>

<label for="status">Status:</label>
<select id="status">
    @foreach ($statuses as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach

    <!-- <option value="xxx">xxx</option> -->
</select>

<table id="pets-table" border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>


@endsection
