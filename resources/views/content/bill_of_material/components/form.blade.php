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
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}"
                                {{ old('product_id', $isEdit ? $product->id : '') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($isEdit)
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
                @if ($isEdit && $billOfMaterial->isNotEmpty())
                    @foreach ($billOfMaterial as $material)
                        <div class="raw-material-row">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-5 col-md-12">
                                    <label class="form-label small d-md-none">Material</label>
                                    <select name="raw_materials[]" class="form-select raw-material-select" required>
                                        <option value="">Select raw material</option>
                                        @foreach ($rawMaterials as $rm)
                                            <option value="{{ $rm->id }}" data-price="{{ $rm->unit_price }}"
                                                {{ $material->raw_material_id == $rm->id ? 'selected' : '' }}>
                                                {{ $rm->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-5 col-sm-6">
                                    <label class="form-label small d-md-none">Quantity</label>
                                    <div class="input-group">
                                        <input type="number" name="quantities[]" class="form-control quantity-input"
                                            placeholder="0"
                                            value="{{ old('quantities.' . $loop->index, $material->quantity) }}"
                                            required min="1">
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
                @else
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
            const rawMaterialsData = @json($rawMaterials->mapWithKeys(fn($item) => [$item->id => ['name' => $item->name, 'price' => $item->unit_price]]));

            const numberFormatter = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            function updateCosts() {
                let total = 0;
                wrapper.querySelectorAll('.raw-material-row').forEach(row => {
                    const select = row.querySelector('.raw-material-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    const costOutput = row.querySelector('.cost-output');
                    const selectedOption = select.selectedOptions[0];

                    const unitPrice = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
                    const quantity = parseFloat(quantityInput.value || 0);
                    const cost = unitPrice * quantity;

                    costOutput.value = isNaN(cost) ? '' : numberFormatter.format(cost);
                    if (!isNaN(cost)) {
                        total += cost;
                    }
                });
                totalCostDisplay.textContent = `Rp ${numberFormatter.format(total)}`;
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

            function createRow() {
                const row = document.createElement('div');
                row.className = 'raw-material-row';

                let optionsHtml = '<option value="">Select raw material</option>';
                for (const id in rawMaterialsData) {
                    optionsHtml +=
                        `<option value="${id}" data-price="${rawMaterialsData[id].price}">${rawMaterialsData[id].name}</option>`;
                }

                row.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-lg-5 col-md-12">
                     <label class="form-label small d-md-none">Material</label>
                    <select name="raw_materials[]" class="form-select raw-material-select" required>${optionsHtml}</select>
                </div>
                <div class="col-lg-3 col-md-5 col-sm-6">
                    <label class="form-label small d-md-none">Quantity</label>
                    <div class="input-group">
                        <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="0" value="1" required min="1">
                    </div>
                </div>
                <div class="col-lg-3 col-md-5 col-sm-6">
                    <label class="form-label small d-md-none">Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control cost-output" placeholder="Cost" readonly>
                    </div>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-12 text-end">
                    <button type="button" class="btn btn-icon btn-danger remove-material" title="Remove Material"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
                wrapper.appendChild(row);
                attachRowListeners(row);
            }

            function attachRowListeners(row) {
                row.querySelector('.remove-material').addEventListener('click', () => {
                    row.remove();
                    updateAll();
                });
            }

            function updateAll() {
                updateCosts();
                disableDuplicateOptions();
            }

            addButton.addEventListener('click', () => {
                createRow();
                updateAll();
            });

            wrapper.addEventListener('input', e => {
                if (e.target.matches('.quantity-input')) {
                    updateAll();
                }
            });

            wrapper.addEventListener('change', e => {
                if (e.target.matches('.raw-material-select')) {
                    updateAll();
                }
            });

            if (wrapper.children.length === 0) {
                createRow();
            }

            wrapper.querySelectorAll('.raw-material-row').forEach(attachRowListeners);

            form.addEventListener("submit", function(e) {
                const selectedValues = [];
                let hasDuplicate = false;
                wrapper.querySelectorAll('.raw-material-select').forEach(select => {
                    const value = select.value;
                    if (value && selectedValues.includes(value)) {
                        hasDuplicate = true;
                    } else if (value) {
                        selectedValues.push(value);
                    }
                });

                if (hasDuplicate) {
                    e.preventDefault();
                    alert(
                        "Duplicate raw materials selected. Please choose unique ingredients for the recipe.");
                }
            });

            updateAll();
        });
    </script>
@endpush
