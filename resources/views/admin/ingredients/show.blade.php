@extends('layouts.admin')

@section('title', 'Detail Bahan Baku')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Bahan Baku</h3>
            <div class="card-tools">
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Nama Bahan</th>
                            <td>{{ $ingredient->name }}</td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>{{ $ingredient->unit ? $ingredient->unit->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Stok Saat Ini</th>
                            <td class="{{ $ingredient->current_stock <= $ingredient->min_stock ? 'text-danger' : '' }}">
                                {{ number_format($ingredient->current_stock, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Stok Minimal</th>
                            <td>{{ number_format($ingredient->min_stock, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Harga Beli</th>
                            <td>Rp {{ number_format($ingredient->unit_cost ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td>{{ $ingredient->barcode ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>{{ $ingredient->branch ? $ingredient->branch->name : 'Pusat' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($ingredient->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Informasi Stok</h5>
                        Total Nilai Stok: Rp {{ number_format($ingredient->current_stock * ($ingredient->unit_cost ?? 0), 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection