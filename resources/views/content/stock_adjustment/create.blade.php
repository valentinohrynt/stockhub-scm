@extends('layouts.master')

@section('title', __('messages.stock_adjustment_form_title'))

@section('content')
    <div class="container py-4">
        <div class="modern-container">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">{{ __('messages.stock_adjustment_form_title') }}</h2>
                    <a href="{{ route('stock_adjustments') }}" class="add-btn"
                        style="background-color: rgba(255,255,255,0.15); color: white; border-color: white;">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_list') }}
                    </a>
                </div>
                <p class="header-subtitle">{{ __('messages.stock_adjustment_form_subtitle') }}</p>
            </div>

            <div class="content-section">
                <div class="modern-form-container">
                    <form action="{{ route('stock_adjustments.store') }}" method="POST" id="stockAdjustmentForm">
                        @csrf

                        <div class="form-section">
                            <h5 class="section-title">{{ __('messages.adjustment_details') }}</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="type_select" class="form-label">{{ __('messages.adjustment_type') }}
                                        {!! __('messages.required_field_indicator') !!}</label>
                                    <select name="type" id="type_select"
                                        class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">{{ __('messages.select_type_placeholder') }}</option>
                                        <option value="addition" {{ old('type') == 'addition' ? 'selected' : '' }}>
                                            {{ __('messages.stock_addition') }}</option>
                                        <option value="deduction" {{ old('type') == 'deduction' ? 'selected' : '' }}>
                                            {{ __('messages.stock_deduction') }}</option>
                                        <option value="initial_stock"
                                            {{ old('type') == 'initial_stock' ? 'selected' : '' }}>
                                            {{ __('messages.initial_stock') }}</option>
                                        <option value="correction" {{ old('type') == 'correction' ? 'selected' : '' }}>
                                            {{ __('messages.correction') }}</option>
                                        <option value="production_usage"
                                            {{ old('type') == 'production_usage' ? 'selected' : '' }}>
                                            {{ __('messages.production_usage') }}
                                        </option>
                                        <option value="breakage" {{ old('type') == 'breakage' ? 'selected' : '' }}>
                                            {{ __('messages.breakage') }}
                                        </option>
                                        <option value="manual_adjustment"
                                            {{ old('type') == 'manual_adjustment' ? 'selected' : '' }}>
                                            {{ __('messages.manual_adjustment') }}
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" id="raw_material_selection_div">
                                    <label for="raw_material_id_input"
                                        class="form-label">{{ __('messages.nav_inventory') }}
                                        {!! __('messages.required_field_indicator') !!}</label>
                                    <select name="raw_material_id" id="raw_material_id_input"
                                        class="form-select @error('raw_material_id') is-invalid @enderror">
                                        <option value="">{{ __('messages.select_raw_material_placeholder_stock') }}
                                        </option>
                                        @foreach ($rawMaterials as $material)
                                            <option value="{{ $material->id }}"
                                                data-stock-unit="{{ $material->stock_unit }}"
                                                data-usage-unit="{{ $material->usage_unit }}"
                                                data-conversion-factor="{{ $material->conversion_factor }}"
                                                {{ old('raw_material_id') == $material->id ? 'selected' : '' }}>
                                                {{ $material->name }} ({{ __('messages.current_stock') }}:
                                                {{ number_format($material->stock, 2) }}
                                                {{ $material->stock_unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('raw_material_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" id="product_selection_div" style="display: none;">
                                    <label for="product_id_input" class="form-label">{{ __('messages.finished_product') }}
                                        {!! __('messages.required_field_indicator') !!}</label>
                                    <select name="product_id" id="product_id_input"
                                        class="form-select @error('product_id') is-invalid @enderror">
                                        <option value="">{{ __('messages.select_product_placeholder_finished') }}
                                        </option>
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
                                    <label for="quantity_input_unit_select"
                                        class="form-label">{{ __('messages.input_quantity_unit') }}
                                        {!! __('messages.required_field_indicator') !!}</label>
                                    <select name="quantity_input_unit" id="quantity_input_unit_select" class="form-select">
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="quantity_input" class="form-label">{{ __('messages.quantity') }} <span
                                            id="quantity_dynamic_unit_label"></span> {!! __('messages.required_field_indicator') !!}</label>
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
                                    <label for="movement_date" class="form-label">{{ __('messages.adjustment_date') }}
                                        {!! __('messages.required_field_indicator') !!}</label>
                                    <input type="date" name="movement_date" id="movement_date"
                                        value="{{ old('movement_date', date('Y-m-d')) }}"
                                        class="form-control @error('movement_date') is-invalid @enderror" required>
                                    @error('movement_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">{{ __('messages.notes_optional') }}</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('stock_adjustments') }}"
                                class="btn btn-outline-secondary">{{ __('messages.cancel_button') }}</a>
                            <button type="submit"
                                class="btn btn-primary">{{ __('messages.save_adjustment_button') }}</button>
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

            const langFinishedProduct = "{{ __('messages.finished_product') }}";
            const langStockUnit = "{{ __('messages.stock_unit') }}";
            const langUsageUnit = "{{ __('messages.usage_unit') }}";
            const langQuantityInUnitLabel =
                "{{ __('messages.quantity_in_unit_label', ['unit_label' => ':unit_placeholder']) }}"; // Use a placeholder
            const langHelpTextFinishedProduct = "{{ __('messages.quantity_help_text_finished_product') }}";
            const langHelpTextDefault = "{{ __('messages.quantity_help_text_default') }}";
            const langHelpTextSelectMaterial = "{{ __('messages.quantity_help_text_select_material') }}";


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
                    quantityDynamicUnitLabel.textContent =
                    `(${langFinishedProduct} ${langStockUnit.toLowerCase()})`;
                    quantityHelpText.textContent = langHelpTextFinishedProduct;
                } else {
                    rawMaterialDiv.style.display = 'block';
                    rawMaterialSelect.setAttribute('required', 'required');
                    productDiv.style.display = 'none';

                    if (selectedRawMaterialOption && selectedRawMaterialOption.value) {
                        if (stockUnit && usageUnit && stockUnit !== usageUnit) {
                            quantityUnitSelectionDiv.style.display = 'block';
                            quantityInputUnitSelect.setAttribute('required', 'required');
                            quantityInputUnitSelect.innerHTML = `
                        <option value="stock_unit" ${ (quantityInputUnitSelect.value === 'stock_unit' || !quantityInputUnitSelect.value) ? 'selected' : '' }>${stockUnit || langStockUnit}</option>
                        <option value="usage_unit" ${ quantityInputUnitSelect.value === 'usage_unit' ? 'selected' : '' }>${usageUnit || langUsageUnit}</option>
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
                    quantityDynamicUnitLabel.textContent =
                    `(${langFinishedProduct} ${langStockUnit.toLowerCase()})`;
                    quantityHelpText.textContent = langHelpTextFinishedProduct;
                    return;
                }

                const selectedRawMaterialOption = rawMaterialSelect.options[rawMaterialSelect.selectedIndex];
                const currentStockUnit = selectedRawMaterialOption ? selectedRawMaterialOption.getAttribute(
                    'data-stock-unit') : langStockUnit;
                const currentUsageUnit = selectedRawMaterialOption ? selectedRawMaterialOption.getAttribute(
                    'data-usage-unit') : langUsageUnit;

                if (quantityUnitSelectionDiv.style.display === 'block' && quantityInputUnitSelect.value) {
                    const selectedQuantityUnit = quantityInputUnitSelect.value;
                    const displayUnit = selectedQuantityUnit === 'stock_unit' ? currentStockUnit : currentUsageUnit;
                    quantityDynamicUnitLabel.textContent =
                        `(${langQuantityInUnitLabel.replace(':unit_placeholder', 'dalam ' + displayUnit)})`;
                    quantityHelpText.textContent = langHelpTextDefault.replace('kuantitas.',
                        `kuantitas dalam ${displayUnit}.`);

                } else if (currentStockUnit) {
                    quantityDynamicUnitLabel.textContent =
                        `(${langQuantityInUnitLabel.replace(':unit_placeholder', 'dalam ' + currentStockUnit)})`;
                    quantityHelpText.textContent = langHelpTextDefault.replace('kuantitas.',
                        `kuantitas dalam ${currentStockUnit}.`);
                } else {
                    quantityDynamicUnitLabel.textContent = '';
                    quantityHelpText.textContent = langHelpTextSelectMaterial;
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
