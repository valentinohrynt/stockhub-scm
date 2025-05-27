@extends('layouts.master')

@section('title', 'Riwayat Pergerakan Stok')

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title mb-0">Riwayat Pergerakan Stok</h2>
                <a href="{{ route('stock_adjustments.create') }}" class="add-btn">
                    <i class="fas fa-plus"></i> Buat Penyesuaian Baru
                </a>
            </div>
        </div>

        <div class="filter-section">
            <div class="filter-card">
                <form action="{{ route('stock_adjustments.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search_raw_material" class="form-label small">Nama Bahan Baku</label>
                            <input type="text" class="form-control" name="search_raw_material"
                                   placeholder="Cari nama bahan baku..." value="{{ request('search_raw_material') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label small">Jenis Pergerakan</label>
                            <select class="form-select" name="type">
                                <option value="">Semua Jenis</option>
                                <option value="addition" {{ request('type') == 'addition' ? 'selected' : '' }}>Penambahan</option>
                                <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>Pengurangan</option>
                                <option value="initial_stock" {{ request('type') == 'initial_stock' ? 'selected' : '' }}>Stok Awal</option>
                                <option value="correction" {{ request('type') == 'correction' ? 'selected' : '' }}>Koreksi</option>
                                <option value="production_usage" {{ request('type') == 'production_usage' ? 'selected' : '' }}>Pemakaian Produksi</option>
                                <option value="breakage" {{ request('type') == 'breakage' ? 'selected' : '' }}>Kerusakan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2 align-self-end">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="submit"><i class="fas fa-filter me-1"></i></button>
                                <a href="{{ route('stock_adjustments.index') }}" class="btn btn-outline-secondary flex-fill"><i class="fas fa-undo me-1"></i></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="content-section">
            @if ($stockMovements->count())
            <div class="table-responsive">
                <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bahan Baku</th>
                            <th>Jenis</th>
                            <th class="text-center">Jumlah</th>
                            <th>Harga Satuan Saat Itu</th>
                            <th>Total Nilai</th>
                            <th>Oleh</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockMovements as $movement)
                            <tr>
                                <td>{{ $movement->movement_date->format('d M Y, H:i') }}</td>
                                <td>
                                    <a href="{{ route('raw_materials.show', $movement->rawMaterial->slug) }}">
                                        {{ $movement->rawMaterial->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $movement->type == 'addition' ? 'success' : ($movement->type == 'deduction' ? 'danger' : 'secondary') }}-subtle">
                                        {{ Str::title(str_replace('_', ' ', $movement->type)) }}
                                    </span>
                                </td>
                                <td class="text-center {{ $movement->type == 'addition' ? 'text-success' : ($movement->type == 'deduction' ? 'text-danger' : '') }}">
                                    {{ $movement->type == 'addition' ? '+' : '-' }} {{ $movement->quantity }}
                                </td>
                                <td>Rp{{ number_format($movement->unit_price_at_movement ?? 0, 2) }}</td>
                                <td>Rp{{ number_format(($movement->unit_price_at_movement ?? 0) * $movement->quantity, 2) }}</td>
                                <td>{{ $movement->user->name ?? 'Sistem' }}</td>
                                <td>{{ Str::limit($movement->notes, 50) ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $stockMovements->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                Tidak ada riwayat pergerakan stok yang ditemukan.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection