@extends('layouts.master')

@section('title', 'Manual Stock Adjustment')

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">Stock Adjustment Form</h2>
                    <a href="{{ route('stock_adjustments') }}" class="add-btn"
                        style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <p class="header-subtitle">Select the adjustment type and fill in the required details.</p>
            </div>

            <div class="content-section">
                <div class="modern-form-container">
                    <form action="{{ route('stock_adjustments.store') }}" method="POST" id="stockAdjustmentForm">
                        @csrf

                        <div class="form-section">
                            <h5 class="section-title">Adjustment Details</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="type_select" class="form-label">Adjustment Type <span
                                            class="text-danger">*</span></label>
                                    <select name="type" id="type_select"
                                        class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="addition" {{ old('type') == 'addition' ? 'selected' : '' }}>
                                            Stock Addition</option>
                                        <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>
                                            Stock Deduction</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" id="raw_material_selection_div">
                                    <label for="raw_material_id_input" class="form-label">Raw Material <span
                                            class="text-danger">*</span></label>
                                    <select name="raw_material_id" id="raw_material_id_input"
                                        class="form-select @error('raw_material_id') is-invalid @enderror">
                                        <option value="">-- Select Raw Material --</option>
                                        @foreach ($rawMaterials as $material)
                                            <option value="{{ $material->id }}"
                                                data-stock-unit="{{ $material->stock_unit }}"
                                                data-usage-unit="{{ $material->usage_unit }}"
                                                data-conversion-factor="{{ $material->conversion_factor }}"
                                                {{ old('raw_material_id') == $material->id ? 'selected' : '' }}>
                                                {{ $material->name }} (Stock: {{ number_format($material->stock, 2) }}
                                                {{ $material->stock_unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('raw_material_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" id="product_selection_div" style="display: none;">
                                    <label for="product_id_input" class="form-label">Finished Product <span
                                            class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id_input"
                                        class="form-select @error('product_id') is-invalid @enderror">
                                        <option value="">-- Select Product --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" id="quantity_unit_selection_div" style="display: none;">
                                    <label for="quantity_input_unit_select" class="form-label">Input Quantity Unit <span
                                            class="text-danger">*</span></label>
                                    <select name="quantity_input_unit" id="quantity_input_unit_select" class="form-select">
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="quantity_input" class="form-label">Quantity <span
                                            id="quantity_dynamic_unit_label"></span> <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="any" name="quantity" id="quantity_input"
                                        value="{{ old('quantity') }}"
                                        class="form-control @error('quantity') is-invalid @enderror" required
                                        min="0.00001">
                                    <div class="form-text" id="quantity_help_text"></div>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="movement_date" class="form-label">Adjustment Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="movement_date" id="movement_date"
                                        value="{{ old('movement_date', date('Y-m-d')) }}"
                                        class="form-control @error('movement_date') is-invalid @enderror" required>
                                    @error('movement_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('stock_adjustments') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Adjustment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type_select');
            const rawMaterialDiv = document.getElementById('raw_material_selection_div');
            const rawMaterialSelect = document.getElementById('raw_material_id_input');
            const productDiv = document.getElementById('product_selection_div');
            const productSelect = document.getElementById('product_id_input');
            const quantityUnitSelectionDiv = document.getElementById('quantity_unit_selection_div');
            const quantityInputUnitSelect = document.getElementById('quantity_input_unit_select');
            const quantityDynamicUnitLabel = document.getElementById('quantity_dynamic_unit_label');
            const quantityHelpText = document.getElementById('quantity_help_text');

            function updateFormBasedOnType() {
                const selectedType = typeSelect.value;
                const selectedRawMaterialOption = rawMaterialSelect.options[rawMaterialSelect.selectedIndex];
                const stockUnit = selectedRawMaterialOption ? selectedRawMaterialOption.getAttribute(
                    'data-stock-unit') : '';
                const usageUnit = selectedRawMaterialOption ? selectedRawMaterialOption.getAttribute(
                    'data-usage-unit') : '';

                productSelect.removeAttribute('required');
                rawMaterialSelect.removeAttribute('required');
                quantityInputUnitSelect.removeAttribute('required');

                if (selectedType === 'production_usage') {
                    rawMaterialDiv.style.display = 'none';
                    productDiv.style.display = 'block';
                    productSelect.setAttribute('required', 'required');
                    quantityUnitSelectionDiv.style.display = 'none';
                    quantityDynamicUnitLabel.textContent = '(Finished Product Units)';
                    quantityHelpText.textContent = 'Enter the number of finished products made.';
                } else {
                    rawMaterialDiv.style.display = 'block';
                    rawMaterialSelect.setAttribute('required', 'required');
                    productDiv.style.display = 'none';

                    if (selectedRawMaterialOption && selectedRawMaterialOption.value) {
                        if (stockUnit && usageUnit && stockUnit !== usageUnit) {
                            quantityUnitSelectionDiv.style.display = 'block';
                            quantityInputUnitSelect.setAttribute('required', 'required');
                            quantityInputUnitSelect.innerHTML = `
                        <option value="stock_unit" ${ (quantityInputUnitSelect.value === 'stock_unit' || !quantityInputUnitSelect.value) ? 'selected' : '' }>${stockUnit || 'Stock Unit'}</option>
                        <option value="usage_unit" ${ quantityInputUnitSelect.value === 'usage_unit' ? 'selected' : '' }>${usageUnit || 'Usage Unit'}</option>
                    `;
                        } else if (stockUnit) {
                            quantityUnitSelectionDiv.style.display = 'none';
                            quantityInputUnitSelect.innerHTML =
                                `<option value="stock_unit" selected>${stockUnit}</option>`;
                        } else {
                            quantityUnitSelectionDiv.style.display = 'none';
                            quantityInputUnitSelect.innerHTML = '';
                        }
                    } else {
                        quantityUnitSelectionDiv.style.display = 'none';
                        quantityInputUnitSelect.innerHTML = '';
                    }
                    updateQuantityLabels();
                }
            }

            function updateQuantityLabels() {
                const selectedType = typeSelect.value;
                if (selectedType === 'production_usage') {
                    quantityDynamicUnitLabel.textContent = '(Finished Product Units)';
                    quantityHelpText.textContent = 'Enter the number of finished products made.';
                    return;
                }

                const selectedRawMaterialOption = rawMaterialSelect.options[rawMaterialSelect.selectedIndex];
                const stockUnit = selectedRawMaterialOption ? selectedRawMaterialOption.getAttribute(
                    'data-stock-unit') : 'Stock Unit';
                const usageUnit = selectedRawMaterialOption ? selectedRawMaterialOption.getAttribute(
                    'data-usage-unit') : 'Usage Unit';

                if (quantityUnitSelectionDiv.style.display === 'block' && quantityInputUnitSelect.value) {
                    const selectedQuantityUnit = quantityInputUnitSelect.value;
                    const displayUnit = selectedQuantityUnit === 'stock_unit' ? stockUnit : usageUnit;
                    quantityDynamicUnitLabel.textContent = `(in ${displayUnit})`;
                    quantityHelpText.textContent = `Enter quantity in ${displayUnit}.`;
                } else if (stockUnit) {
                    quantityDynamicUnitLabel.textContent = `(in ${stockUnit})`;
                    quantityHelpText.textContent = `Enter quantity in ${stockUnit}.`;
                } else {
                    quantityDynamicUnitLabel.textContent = '';
                    quantityHelpText.textContent = 'Select a raw material first.';
                }
            }

            if (typeSelect) typeSelect.addEventListener('change', updateFormBasedOnType);
            if (rawMaterialSelect) rawMaterialSelect.addEventListener('change', function() {
                updateFormBasedOnType();
            });
            if (quantityInputUnitSelect) quantityInputUnitSelect.addEventListener('change', updateQuantityLabels);
            updateFormBasedOnType();
        });
    </script>
@endpush
