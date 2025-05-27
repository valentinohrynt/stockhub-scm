@php
    $isEdit = isset($rawMaterial);
    $route = $isEdit ? route('raw_materials.update', $rawMaterial->slug) : route('raw_materials.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="modern-form-container">
    <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method($method)
        @endif

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.basic_information') }}</h5>
            <div class="row g-3">
                <div class="col-md-7">
                    <label for="name" class="form-label">{{ __('messages.raw_material_name') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $isEdit ? $rawMaterial->name : '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label for="category_id" class="form-label">{{ __('messages.category') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <select name="category_id" id="category_id"
                        class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_category_placeholder') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $isEdit ? $rawMaterial->category_id : '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <label for="supplier_id" class="form-label">{{ __('messages.nav_suppliers') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <select name="supplier_id" id="supplier_id"
                        class="form-select @error('supplier_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_supplier_placeholder') }}</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id', $isEdit ? $rawMaterial->supplier_id : '') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.stock_units_pricing') }}</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="stock" class="form-label">{{ __('messages.current_stock') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <input type="number" step="any" name="stock" id="stock"
                        class="form-control @error('stock') is-invalid @enderror"
                        value="{{ old('stock', $isEdit ? $rawMaterial->stock : 0) }}" required>
                    <div class="form-text">
                        {{ __('messages.current_stock_in_unit_info', ['unit' => '<strong id="displayStockUnit1">' . old('stock_unit', $isEdit ? $rawMaterial->stock_unit : __('messages.stock_unit')) . '</strong>']) }}
                    </div>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="stock_unit_input" class="form-label">{{ __('messages.stock_unit') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="stock_unit" id="stock_unit_input"
                        class="form-control @error('stock_unit') is-invalid @enderror"
                        value="{{ old('stock_unit', $isEdit ? $rawMaterial->stock_unit : '') }}"
                        placeholder="{{ __('messages.stock_unit_placeholder') }}" required>
                    <div class="form-text">{{ __('messages.stock_unit_info') }}</div>
                    @error('stock_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="unit_price" class="form-label">{{ __('messages.unit_price') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="unit_price" id="unit_price" step="any" min="0"
                            class="form-control @error('unit_price') is-invalid @enderror"
                            value="{{ old('unit_price', $isEdit ? $rawMaterial->unit_price : '') }}" required>
                    </div>
                    <div class="form-text">
                        {{ __('messages.unit_price_info', ['stock_unit' => '<strong id="displayStockUnit2">' . old('stock_unit', $isEdit ? $rawMaterial->stock_unit : __('messages.stock_unit')) . '</strong>']) }}
                    </div>
                    @error('unit_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="usage_unit_input" class="form-label">{{ __('messages.usage_unit') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="usage_unit" id="usage_unit_input"
                        class="form-control @error('usage_unit') is-invalid @enderror"
                        value="{{ old('usage_unit', $isEdit ? $rawMaterial->usage_unit : '') }}"
                        placeholder="{{ __('messages.usage_unit_placeholder') }}" required>
                    <div class="form-text">{{ __('messages.usage_unit_info') }}</div>
                    @error('usage_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8">
                    <label for="conversion_factor" class="form-label">{{ __('messages.conversion_factor') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <input type="number" step="any" name="conversion_factor" id="conversion_factor"
                        class="form-control @error('conversion_factor') is-invalid @enderror"
                        value="{{ old('conversion_factor', $isEdit ? $rawMaterial->conversion_factor : '') }}"
                        required>
                    <div class="form-text">
                        {{ __('messages.conversion_factor_info', [
                            'usage_unit' =>
                                '<strong id="displayUsageUnit">' .
                                old('usage_unit', $isEdit ? $rawMaterial->usage_unit : __('messages.usage_unit')) .
                                '</strong>',
                            'stock_unit' =>
                                '<strong id="displayStockUnit3">' .
                                old('stock_unit', $isEdit ? $rawMaterial->stock_unit : __('messages.stock_unit')) .
                                '</strong>',
                        ]) }}
                    </div>
                    @error('conversion_factor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.status_and_image') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="is_active" class="form-label">{{ __('messages.status') }}</label>
                    <select name="is_active" id="is_active"
                        class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1"
                            {{ old('is_active', $isEdit ? $rawMaterial->is_active : 1) == 1 ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="0"
                            {{ old('is_active', $isEdit ? $rawMaterial->is_active : 1) == 0 ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="image_path_raw_material" class="form-label">{{ __('messages.image') }}</label>
                    <div class="image-upload-wrapper">
                        <input type="file" name="image_path" id="image_path_raw_material"
                            class="image-upload-input" accept="image/*">
                        <div class="image-upload-placeholder">
                            <img id="image-preview-raw-material"
                                src="{{ $isEdit && $rawMaterial->image_path ? Storage::url($rawMaterial->image_path) : '' }}"
                                alt="{{ __('messages.image_preview') }}"
                                class="{{ $isEdit && $rawMaterial->image_path ? 'active' : '' }}">
                            <div class="placeholder-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>{{ __('messages.upload_raw_material_image') }}</p>
                            </div>
                        </div>
                    </div>
                    @error('image_path')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.inventory_management_jit') }}</h5>
            <p class="form-text mb-3">{{ __('messages.jit_parameters_info') }}</p>
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <label for="lead_time" class="form-label">{{ __('messages.lead_time_days') }}</label>
                    <input type="number" name="lead_time" id="lead_time" min="0"
                        class="form-control @error('lead_time') is-invalid @enderror"
                        value="{{ old('lead_time', $isEdit ? $rawMaterial->lead_time ?? 0 : 0) }}">
                    <div class="form-text">{{ __('messages.supplier_waiting_time') }}</div>
                    @error('lead_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="safety_stock_days"
                        class="form-label">{{ __('messages.safety_stock_policy') }}</label>
                    <input type="number" name="safety_stock_days" id="safety_stock_days" min="0"
                        class="form-control @error('safety_stock_days') is-invalid @enderror"
                        value="{{ old('safety_stock_days', $isEdit ? $rawMaterial->safety_stock_days ?? 0 : 0) }}">
                    <div class="form-text">{{ __('messages.coverage_in_days') }}</div>
                    @error('safety_stock_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="replenish_quantity" class="form-label">{{ __('messages.reorder_quantity') }}</label>
                    <input type="number" name="replenish_quantity" id="replenish_quantity" min="1"
                        class="form-control @error('replenish_quantity') is-invalid @enderror"
                        value="{{ old('replenish_quantity', $isEdit ? $rawMaterial->replenish_quantity ?? 1 : 1) }}">
                    <div class="form-text">{!! __('messages.units_per_order_in_stock_unit', [
                        'unit' =>
                            '<strong id="displayStockUnit4">' .
                            old('stock_unit', $isEdit ? $rawMaterial->stock_unit : __('messages.stock_unit')) .
                            '</strong>',
                    ]) !!}
                    </div>
                    @error('replenish_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6 col-lg-3">
                    <label for="safety_stock" class="form-label">{!! __('messages.safety_stock_auto_in_stock_unit', [
                        'unit' =>
                            '<strong id="displayStockUnit5">' .
                            old('stock_unit', $isEdit ? $rawMaterial->stock_unit : __('messages.stock_unit')) .
                            '</strong>',
                    ]) !!}</label>
                    <input type="number" step="any" name="safety_stock" id="safety_stock" class="form-control"
                        value="{{ old('safety_stock', $isEdit ? $rawMaterial->safety_stock ?? 0 : 0) }}" disabled
                        readonly>
                    <div class="form-text">{{ __('messages.avg_usage_x_safety_days') }}</div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="signal_point" class="form-label">{!! __('messages.signal_point_auto_in_stock_unit', [
                        'unit' =>
                            '<strong id="displayStockUnit6">' .
                            old('stock_unit', $isEdit ? $rawMaterial->stock_unit : __('messages.stock_unit')) .
                            '</strong>',
                    ]) !!}</label>
                    <input type="number" step="any" name="signal_point" id="signal_point" class="form-control"
                        value="{{ old('signal_point', $isEdit ? $rawMaterial->signal_point ?? 0 : 0) }}" disabled
                        readonly>
                    <div class="form-text">{{ __('messages.avg_usage_x_lead_time_plus_safety_stock') }}</div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.description') }}</h5>
            <textarea name="description" id="description" rows="4"
                class="form-control @error('description') is-invalid @enderror">{{ old('description', $isEdit ? $rawMaterial->description : '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('raw_materials') }}"
                class="btn btn-outline-secondary">{{ __('messages.cancel_button') }}</a>
            <button type="submit"
                class="btn btn-primary">{{ $isEdit ? __('messages.update_button') : __('messages.create_button') }}
                {{ __('messages.nav_inventory') }}</button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image_path_raw_material');
            const imagePreview = document.getElementById('image-preview-raw-material');
            const placeholderContent = imageInput ? imageInput.closest('.image-upload-wrapper').querySelector(
                '.placeholder-content') : null;

            if (imageInput && imagePreview && placeholderContent) {
                imageInput.addEventListener('change', function(event) {
                    if (event.target.files && event.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.classList.add('active');
                            placeholderContent.style.display = 'none';
                        }
                        reader.readAsDataURL(event.target.files[0]);
                    } else {
                        // This part handles if the user cancels file selection - it might need adjustment based on exact desired behavior
                        if (!imagePreview.dataset.defaultSrc) { // Store original src if not already done
                            imagePreview.dataset.defaultSrc = imagePreview.src;
                        }
                        if (!imageInput
                            .value) { // If input is cleared (some browsers might not trigger change for this)
                            imagePreview.src = imagePreview.dataset.defaultSrc ||
                            ''; // Revert to original or empty
                            if (imagePreview.src && imagePreview.src !== window.location
                                .href) { // If there was an initial image
                                imagePreview.classList.add('active');
                                placeholderContent.style.display = 'none';
                            } else {
                                imagePreview.classList.remove('active');
                                placeholderContent.style.display = 'block';
                            }
                        }
                    }
                });
                // Initial check in case there's already an image (e.g., on edit form)
                if (imagePreview.src && imagePreview.src !== window.location
                    .href) { // window.location.href is a trick if src is empty
                    imagePreview.classList.add('active');
                    placeholderContent.style.display = 'none';
                } else {
                    imagePreview.classList.remove('active');
                    placeholderContent.style.display = 'block';
                }
            }

            const stockUnitInput = document.getElementById('stock_unit_input');
            const usageUnitInput = document.getElementById('usage_unit_input');
            const displayStockUnits = [
                document.getElementById('displayStockUnit1'),
                document.getElementById('displayStockUnit2'),
                document.getElementById('displayStockUnit3'),
                document.getElementById('displayStockUnit4'),
                document.getElementById('displayStockUnit5'),
                document.getElementById('displayStockUnit6')
            ].filter(el => el !== null); // Filter out nulls in case some IDs are not present
            const displayUsageUnit = document.getElementById('displayUsageUnit');

            function updateUnitDisplays() {
                const stockUnitValue = stockUnitInput.value || '{{ __('messages.stock_unit') }}';
                const usageUnitValue = usageUnitInput.value || '{{ __('messages.usage_unit') }}';

                displayStockUnits.forEach(el => {
                    if (el) el.textContent = stockUnitValue;
                });
                if (displayUsageUnit) displayUsageUnit.textContent = usageUnitValue;
            }

            if (stockUnitInput) stockUnitInput.addEventListener('input', updateUnitDisplays);
            if (usageUnitInput) usageUnitInput.addEventListener('input', updateUnitDisplays);

            // Initial call to set texts
            updateUnitDisplays();
        });
    </script>
@endpush
