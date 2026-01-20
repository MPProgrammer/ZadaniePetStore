@extends('layouts.app')

@section('title', 'Lista zwierząt')
@section('h1_title', 'Lista zwierząt')

@section('header')
    <a href="{{ route('pets.create') }}" class="button button-primary">Dodaj zwierzę</a>
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
                    <th class="column-name">Nazwa</th>
                    <th class="column-status">Status</th>
                    <th class="column-actions">Akcje</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@endsection
