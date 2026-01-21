@extends('layouts.app')

@section('title', 'Pet list')
@section('h1_title', 'Pet list')

@section('header')
    <a href="{{ route('pets.create') }}" class="button button-primary">Add</a>
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

    <div id="filters">
        <label for="status">Status:</label>
        <select id="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div id="pets-table-container">
        <table id="pets-table">
            <thead>
                <tr>
                    <th class="column-id">ID</th>
                    <th class="column-name">Name</th>
                    <th class="column-status">Status</th>
                    <th class="column-actions">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@endsection
