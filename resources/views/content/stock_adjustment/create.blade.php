@extends('layouts.master')

@section('title', 'Penyesuaian Stok Manual')

@section('content')
<div class="container py-4">
    <div class="modern-container">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">Form Penyesuaian Stok</h2>
                <a href="{{ route('stock_adjustments') }}" class="add-btn" style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke List
                </a>
            </div>
            <p class="header-subtitle">Pilih jenis penyesuaian dan isi detail yang diperlukan.</p>
        </div>

        <div class="content-section">
            <div class="modern-form-container">
                <form action="{{ route('stock_adjustments.store') }}" method="POST" id="stockAdjustmentForm">
                    @csrf

                    <div class="form-section">
                        <h5 class="section-title">Detail Penyesuaian</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="raw_material_id" class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                                <select name="raw_material_id" id="raw_material_id" class="form-select @error('raw_material_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Bahan Baku --</option>
                                    @foreach ($rawMaterials as $material)
                                        <option value="{{ $material->id }}" {{ old('raw_material_id') == $material->id ? 'selected' : '' }}>
                                            {{ $material->name }} (Stok Saat Ini: {{ $material->stock }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('raw_material_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Jenis Penyesuaian <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="addition" {{ old('type') == 'addition' ? 'selected' : '' }}>Penambahan Stok</option>
                                    <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>Pengurangan Stok</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror" required min="1">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="movement_date" class="form-label">Tanggal Penyesuaian <span class="text-danger">*</span></label>
                                <input type="date" name="movement_date" id="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" class="form-control @error('movement_date') is-invalid @enderror" required>
                                @error('movement_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('raw_materials') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection