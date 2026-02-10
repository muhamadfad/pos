@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>{{ isset($mitra) ? 'Edit Mitra' : 'Tambah Mitra' }}</h3>

    <form 
        action="{{ isset($mitra) ? route('admin.mitra.update', $mitra->id) : route('admin.mitra.store') }}" 
        method="POST"
    >
        @csrf
        @if(isset($mitra))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Nama Mitra</label>
            <input type="text" name="nama_mitra" class="form-control"
                   value="{{ old('nama_mitra', $mitra->nama_mitra ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control"
                   value="{{ old('alamat', $mitra->alamat ?? '') }}">
        </div>

        <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control"
                   value="{{ old('no_hp', $mitra->no_hp ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Jenis Mitra</label>
            <input type="text" name="jenis_mitra" class="form-control"
                   value="{{ old('jenis_mitra', $mitra->jenis_mitra ?? '') }}"
                   placeholder="Depo Bangunan / Toko Material / dll">
        </div>

        <button type="submit" class="btn btn-success">
            {{ isset($mitra) ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('admin.mitra.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
