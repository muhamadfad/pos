@extends('layouts.app')

@section('title', 'Tambah Stok Bahan Baku')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Stok Bahan Baku</h3>
            <div class="card-tools">
                <a href="{{ route('inventory.branch-stock.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        
        <form action="{{ route('inventory.branch-stock.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cabang <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                                <option value="">Pilih Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bahan Baku <span class="text-danger">*</span></label>
                            <select name="ingredient_id" class="form-control @error('ingredient_id') is-invalid @enderror" required>
                                <option value="">Pilih Bahan Baku</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" {{ old('ingredient_id') == $ingredient->id ? 'selected' : '' }}>
                                        {{ $ingredient->name }} ({{ $ingredient->unit->name ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ingredient_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 0) }}" required>
                            @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stok Minimal</label>
                            <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror" value="{{ old('min_stock', 0) }}">
                            <small class="text-muted">Peringatan jika stok di bawah nilai ini</small>
                            @error('min_stock')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stok Maksimal</label>
                            <input type="number" name="max_stock" class="form-control @error('max_stock') is-invalid @enderror" value="{{ old('max_stock', 0) }}">
                            <small class="text-muted">Batasan stok maksimal</small>
                            @error('max_stock')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('inventory.branch-stock.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
