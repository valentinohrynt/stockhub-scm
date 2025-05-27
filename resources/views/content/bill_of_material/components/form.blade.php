@php
    $isEdit = isset($product);
    $route = $isEdit ? route('bill_of_materials.update', $product->slug) : route('bill_of_materials.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="modern-form-container">
    <form action="{{ $route }}" method="POST" id="bom-form">
        @csrf
        @if ($isEdit)
            @method($method)
        @endif

        <div class="form-section">
            <h5 class="section-title">Product</h5>
            <div class="row">
                <div class="col-md-12">
                    <label for="product_id" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id"
                        class="form-select @error('product_id') is-invalid @enderror" required
                        {{ $isEdit ? 'disabled' : '' }}>
                        <option value="">Select a product...</option>
                        @foreach ($products ?? ($allProducts ?? []) as $p)
                            <option value="{{ $p->id }}"
                                {{ old('product_id', $isEdit && isset($product) ? $product->id : '') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($isEdit && isset($product))
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @endif
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Recipe Ingredients</h5>
                <button type="button" id="add-material" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>
                    Add Material</button>
            </div>

            <div id="raw-materials-wrapper">
                @if ($isEdit && isset($billOfMaterial) && $billOfMaterial->isNotEmpty())
                    @foreach ($billOfMaterial as $index => $material_item)
                        <div class="raw-material-row mb-3 p-3 border rounded" data-row-index="{{ $index }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-5 col-md-12">
                                    <label class="form-label small d-md-none">Material</label>
                                    <select name="raw_materials[{{ $index }}][id]"
                                        class="form-select raw-material-select" data-index="{{ $index }}"
                                        required>
                                        <option value="">Select raw material</option>
                                        @foreach ($rawMaterials as $rm)
                                            <option value="{{ $rm->id }}" data-price="{{ $rm->unit_price }}"
                                                data-stock-unit="{{ $rm->stock_unit }}"
                                                data-usage-unit="{{ $rm->usage_unit }}"
                                                data-conversion-factor="{{ $rm->conversion_factor }}"
                                                {{ ($material_item->raw_material_id ?? '') == $rm->id ? 'selected' : '' }}>
                                                {{ $rm->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-5 col-sm-6">
                                    <label class="form-label small d-md-none">Quantity</label>
                                    <div class="input-group">
                                        <input type="number" step="any"
                                            name="raw_materials[{{ $index }}][quantity]"
                                            class="form-control quantity-input" placeholder="0"
                                            value="{{ old('raw_materials.' . $index . '.quantity', $material_item->quantity ?? 1) }}"
                                            required min="0.00001">
                                        <span
                                            class="input-group-text usage-unit-display">{{ $material_item->rawMaterial->usage_unit ?? 'Usage Unit' }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-5 col-sm-6">
                                    <label class="form-label small d-md-none">Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control cost-output" placeholder="Cost"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-2 col-sm-12 text-end">
                                    <button type="button" class="btn btn-icon btn-danger remove-material"
                                        title="Remove Material">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="form-section summary-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="total-cost-display">
                    <strong>Total Base Cost:</strong>
                    <span id="total-cost-value">Rp 0.00</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('bill_of_materials') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }} Recipe</button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("bom-form");
            const wrapper = document.getElementById('raw-materials-wrapper');
            const addButton = document.getElementById('add-material');
            const totalCostDisplay = document.getElementById('total-cost-value');
            const rawMaterialsData = {!! json_encode(
                $rawMaterials->mapWithKeys(
                    fn($item) => [
                        $item->id => [
                            'name' => $item->name,
                            'price_per_stock_unit' => $item->unit_price,
                            'stock_unit' => $item->stock_unit,
                            'usage_unit' => $item->usage_unit,
                            'conversion_factor' => $item->conversion_factor,
                        ],
                    ],
                ),
            ) !!};

            const numberFormatter = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            let materialIndex = {{ $isEdit && isset($billOfMaterial) ? $billOfMaterial->count() : 0 }};

            function updateCostsAndUnits() {
                let totalRecipeCost = 0;
                wrapper.querySelectorAll('.raw-material-row').forEach(row => {
                    const select = row.querySelector('.raw-material-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    const costOutput = row.querySelector('.cost-output');
                    const usageUnitDisplay = row.querySelector('.usage-unit-display');
                    const selectedOption = select.options[select.selectedIndex];
                    const materialId = select.value;
                    const materialInfo = rawMaterialsData[materialId];

                    if (materialInfo) {
                        const pricePerStockUnit = parseFloat(materialInfo.price_per_stock_unit || 0);
                        const conversionFactor = parseFloat(materialInfo.conversion_factor || 1);
                        const quantityInUsageUnit = parseFloat(quantityInput.value || 0);
                        let cost = 0;
                        if (conversionFactor > 0) {
                            const quantityInStockUnit = quantityInUsageUnit / conversionFactor;
                            cost = pricePerStockUnit * quantityInStockUnit;
                        }
                        costOutput.value = isNaN(cost) ? '0.00' : numberFormatter.format(cost);
                        if (!isNaN(cost)) {
                            totalRecipeCost += cost;
                        }
                        usageUnitDisplay.textContent = materialInfo.usage_unit || 'Usage Unit';
                    } else {
                        costOutput.value = '0.00';
                        usageUnitDisplay.textContent = 'Usage Unit';
                    }
                });
                totalCostDisplay.textContent = `Rp ${numberFormatter.format(totalRecipeCost)}`;
            }

            function disableDuplicateOptions() {
                const selects = Array.from(wrapper.querySelectorAll('.raw-material-select'));
                const selectedValues = selects.map(s => s.value).filter(Boolean);
                selects.forEach(select => {
                    Array.from(select.options).forEach(option => {
                        if (option.value && selectedValues.includes(option.value) && select
                            .value !== option.value) {
                            option.disabled = true;
                        } else {
                            option.disabled = false;
                        }
                    });
                });
            }

            function createRowHtml(index) {
                let optionsHtml = '<option value="">Select raw material</option>';
                for (const id in rawMaterialsData) {
                    const mat = rawMaterialsData[id];
                    optionsHtml +=
                        `<option value="<span class="math-inline">\{id\}" data\-price\="</span>{mat.price_per_stock_unit}" data-stock-unit="<span class="math-inline">\{mat\.stock\_unit\}" data\-usage\-unit\="</span>{mat.usage_unit}" data-conversion-factor="<span class="math-inline">\{mat\.conversion\_factor\}"\></span>{mat.name}</option>`;
                }
                return `
                <div class="raw-material-row mb-3 p-3 border rounded" data-row-index="<span class="math-inline">\{index\}"\>
<div class\="row g\-2 align\-items\-center"\>
<div class\="col\-lg\-5 col\-md\-12"\>
<select name\="raw\_materials\[</span>{index}][id]" class="form-select raw-material-select" data-index="<span class="math-inline">\{index\}" required\></span>{optionsHtml}</select>
                        </div>
                        <div class="col-lg-3 col-md-5 col-sm-6">
                            <div class="input-group">
                                <input type="number" step="any" name="raw_materials[${index}][quantity]" class="form-control quantity-input" placeholder="0" value="1" required min="0.00001">
                                <span class="input-group-text usage-unit-display">Usage Unit</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5 col-sm-6">
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control cost-output" placeholder="Cost" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-12 text-end">
                            <button type="button" class="btn btn-icon btn-danger remove-material" title="Remove Material"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>`;
            }

            function addRowListeners(rowElement) {
                rowElement.querySelector('.remove-material').addEventListener('click', () => {
                    rowElement.remove();
                    updateAll();
                });
                rowElement.querySelector('.raw-material-select').addEventListener('change', updateAll);
                rowElement.querySelector('.quantity-input').addEventListener('input', updateAll);
            }

            function addNewRow() {
                const newRowDiv = document.createElement('div');
                newRowDiv.innerHTML = createRowHtml(materialIndex);
                const newRowElement = newRowDiv.firstElementChild;
                wrapper.appendChild(newRowElement);
                addRowListeners(newRowElement);
                materialIndex++;
                updateAll();
            }

            function updateAll() {
                updateCostsAndUnits();
                disableDuplicateOptions();
            }

            addButton.addEventListener('click', addNewRow);
            wrapper.querySelectorAll('.raw-material-row').forEach(row => {
                addRowListeners(row);
            });

            if (wrapper.children.length === 0 && !
                {{ $isEdit && isset($billOfMaterial) && $billOfMaterial->isNotEmpty() ? 'true' : 'false' }}) {
                addNewRow();
            }

            form.addEventListener("submit", function(e) {
                const selects = Array.from(wrapper.querySelectorAll('.raw-material-select'));
                if (selects.length === 0) {
                    alert("Please add at least one raw material to the recipe.");
                    e.preventDefault();
                    return;
                }
                const selectedValues = [];
                let hasDuplicate = false;
                let hasEmptySelection = false;
                selects.forEach(select => {
                    const value = select.value;
                    if (!value) {
                        hasEmptySelection = true;
                    }
                    if (value && selectedValues.includes(value)) {
                        hasDuplicate = true;
                    } else if (value) {
                        selectedValues.push(value);
                    }
                });
                if (hasEmptySelection) {
                    alert("Please select a raw material for all rows.");
                    e.preventDefault();
                    return;
                }
                if (hasDuplicate) {
                    alert(
                        "Duplicate raw materials selected. Please choose unique ingredients for the recipe."
                        );
                    e.preventDefault();
                    return;
                }
            });
            updateAll();
        });
    </script>
@endpush
