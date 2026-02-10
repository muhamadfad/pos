@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Data Mitra / Depo Bangunan</h3>
    <a href="{{ route('mitra.create') }}" class="btn btn-primary mb-3">Tambah Mitra</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mitra</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mitras as $mitra)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mitra->nama_mitra }}</td>
                <td>{{ $mitra->alamat }}</td>
                <td>{{ $mitra->no_hp }}</td>
                <td>{{ $mitra->jenis_mitra }}</td>
                <td>
                    <a href="{{ route('mitra.edit', $mitra->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('mitra.destroy', $mitra->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
