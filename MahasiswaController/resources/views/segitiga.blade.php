@extends('layouts.app')

@section('title', 'Luas Segitiga')

@section('content')
    <h1>Kalkulator Luas Segitiga</h1>

    <form action="{{ route('kalkulator.segitiga') }}" method="GET" class="card">
        <label for="alas">Alas</label>
        <input type="number" step="any" min="0" id="alas" name="alas" value="{{ $alas }}" required>

        <label for="tinggi">Tinggi</label>
        <input type="number" step="any" min="0" id="tinggi" name="tinggi" value="{{ $tinggi }}" required>

        <button type="submit">Hitung</button>
    </form>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    @if ($luas !== null)
        <div class="hasil">
            Luas = &frac12; &times; {{ $alas }} &times; {{ $tinggi }} =
            <strong>{{ round($luas, 2) }}</strong>
        </div>
    @endif
@endsection