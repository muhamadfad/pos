@extends('layouts.app')

@section('title', 'Manajemen Stok Bahan Baku')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Stok Bahan Baku per Cabang</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.branch-stock.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Stok
                        </a>
                        <a href="{{ route('inventory.branch-stock.low-stock') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-exclamation-triangle"></i> Stok Menipis
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_items']) }}</h3>
                                    <p>Total Item Stok</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($stats['low_stock']) }}</h3>
                                    <p>Stok Menipis</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ number_format($stats['out_of_stock']) }}</h3>
                                    <p>Stok Habis</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h3>
                                    <p>Total Nilai Stok</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="branch_id" class="form-control">
                                    <option value="">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="ingredient_id" class="form-control">
                                    <option value="">Semua Bahan Baku</option>
                                    @foreach($ingredients as $ingredient)
                                        <option value="{{ $ingredient->id }}" {{ request('ingredient_id') == $ingredient->id ? 'selected' : '' }}>
                                            {{ $ingredient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="stock_status" class="form-control">
                                    <option value="">Semua Stok</option>
                                    <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Stok Normal</option>
                                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
                                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Cabang</th>
                                    <th>Bahan Baku</th>
                                    <th>Stok Saat Ini</th>
                                    <th>Stok Minimal</th>
                                    <th>Stok Maksimal</th>
                                    <th>Status</th>
                                    <th>Terakhir Update</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branchStocks as $key => $stock)
                                <tr>
                                    <td>{{ $branchStocks->firstItem() + $key }}</td>
                                    <td>{{ $stock->branch->name }}</td>
                                    <td>{{ $stock->ingredient->name }}</td>
                                    <td class="{{ $stock->quantity <= $stock->min_stock ? 'text-danger font-weight-bold' : '' }}">
                                        {{ number_format($stock->quantity) }}
                                    </td>
                                    <td>{{ number_format($stock->min_stock) }}</td>
                                    <td>{{ number_format($stock->max_stock) }}</td>
                                    <td>
                                        @if($stock->quantity == 0)
                                            <span class="badge badge-danger">Stok Habis</span>
                                        @elseif($stock->quantity <= $stock->min_stock)
                                            <span class="badge badge-warning">Stok Menipis</span>
                                        @else
                                            <span class="badge badge-success">Normal</span>
                                        @endif
                                    </td>
                                    <td>{{ $stock->last_updated ? $stock->last_updated->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('inventory.branch-stock.show', $stock->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.branch-stock.edit', $stock->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $stock->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="adjustStock({{ $stock->id }}, '{{ $stock->ingredient->name }}', {{ $stock->quantity }})">
                                            <i class="fas fa-adjust"></i> Adjust
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data stok</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $branchStocks->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus stok ini?</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sesuaikan Stok</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="adjustForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Bahan Baku: <strong id="ingredientName"></strong></p>
                    <p>Stok Saat Ini: <strong id="currentStock"></strong></p>
                    
                    <div class="form-group">
                        <label>Penyesuaian (+ untuk tambah, - untuk kurangi)</label>
                        <input type="number" name="adjustment" class="form-control" required>
                        <small class="text-muted">Contoh: 10 (tambah 10) atau -5 (kurangi 5)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Alasan (Opsional)</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    let form = document.getElementById('deleteForm');
    form.action ="{{ route('inventory.branch-stock.index') }}/" + id;
    $('#deleteModal').modal('show');
}

function adjustStock(id, name, stock) {
    document.getElementById('ingredientName').innerText = name;
    document.getElementById('currentStock').innerText = stock;
    let form = document.getElementById('adjustForm');
    form.action ="{{ url('inventory/branch-stock') }}/" + id +"/adjust";
    $('#adjustModal').modal('show');
}

$('#adjustForm').on('submit', function(e) {
    e.preventDefault();
    let form = $(this);
    let url = form.attr('action');
    
    $.ajax({
        url: url,
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if(response.success) {
                Swal.fire('Sukses!', response.message, 'success').then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr) {
            let error = xhr.responseJSON?.error || 'Terjadi kesalahan';
            Swal.fire('Error!', error, 'error');
        }
    });
});
</script>
@endpush
