@extends('layouts.app')

@section('title', 'Lista petów')

@section('content')

<h2>Lista petów</h2>


@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

@if($errors->any())
    <p style="color:red">{{ $errors->first() }}</p>
@endif

<a href="{{ route('pets.create') }}">Add pet</a>

<label for="status">Status:</label>
<select id="status">
    @foreach ($statuses as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach

    <!-- <option value="xxx">xxx</option> -->
</select>

<table border="1" cellpadding="5" id="pets-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3">Loading...</td>
        </tr>
    </tbody>
</table>

@endsection
