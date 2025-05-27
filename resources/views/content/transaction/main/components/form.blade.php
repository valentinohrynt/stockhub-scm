<div class="modern-form-container">
    <form action="{{ route('transactions.store') }}" method="POST" id="transaction-form">
        @csrf

        <div class="form-section">
            <h5 class="section-title">Customer & Table Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name"
                        class="form-control @error('customer_name') is-invalid @enderror"
                        value="{{ old('customer_name') }}">
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="table_number" class="form-label">Table Number</label>
                    <input type="text" name="table_number" id="table_number"
                        class="form-control @error('table_number') is-invalid @enderror"
                        value="{{ old('table_number') }}">
                    @error('table_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Order Items</h5>
                <button type="button" id="add-product" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>
                    Add Product</button>
            </div>

            <div id="product-list">
                @if (old('products'))
                    @foreach (old('products') as $key => $oldProduct)
                        <div class="product-item raw-material-row {{ $loop->first ? '' : 'mt-2' }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                                    <label class="form-label small d-lg-none">Product <span
                                            class="text-danger">*</span></label>
                                    <select name="products[{{ $key }}][product_id]"
                                        class="form-select product-select" required>
                                        <option value="">-- Select Product --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->selling_price }}"
                                                {{ isset($oldProduct['product_id']) && $oldProduct['product_id'] == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} -
                                                Rp{{ number_format($product->selling_price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-5 mb-2 mb-lg-0">
                                    <label class="form-label small d-lg-none">Quantity <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="products[{{ $key }}][quantity]"
                                        class="form-control quantity-input" min="1"
                                        value="{{ $oldProduct['quantity'] ?? 1 }}" required>
                                </div>
                                <div class="col-lg-3 col-md-5 col-sm-7 mb-2 mb-lg-0">
                                    <label class="form-label small d-lg-none">Subtotal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control price-display" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12 text-lg-end">
                                    <button type="button" class="btn btn-icon btn-danger remove-product"
                                        title="Remove Product">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="product-item raw-material-row">
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                                <label class="form-label small d-lg-none">Product <span
                                        class="text-danger">*</span></label>
                                <select name="products[0][product_id]" class="form-select product-select" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-price="{{ $product->selling_price }}">
                                            {{ $product->name }} -
                                            Rp{{ number_format($product->selling_price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-5 mb-2 mb-lg-0">
                                <label class="form-label small d-lg-none">Quantity <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="products[0][quantity]" class="form-control quantity-input"
                                    min="1" value="1" required>
                            </div>
                            <div class="col-lg-3 col-md-5 col-sm-7 mb-2 mb-lg-0">
                                <label class="form-label small d-lg-none">Subtotal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control price-display" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-12 text-lg-end">
                                <button type="button" class="btn btn-icon btn-danger remove-product"
                                    title="Remove Product">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @error('products')
                <div class="text-danger mt-2 small">{{ $message }}</div>
            @enderror
            @error('products.*.product_id')
                <div class="text-danger mt-2 small">{{ $message }}</div>
            @enderror
            @error('products.*.quantity')
                <div class="text-danger mt-2 small">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-section summary-section">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span
                                class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method"
                            class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                            </option>
                            <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS
                            </option>
                            <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>
                                Debit Card
                            </option>
                            <option value="credit_card"
                                {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card
                            </option>
                            <option value="bank_transfer"
                                {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank
                                Transfer</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_status" class="form-label">Payment Status <span
                                class="text-danger">*</span></label>
                        <select name="payment_status" id="payment_status"
                            class="form-select @error('payment_status') is-invalid @enderror" required>
                            <option value="unpaid"
                                {{ old('payment_status', 'unpaid') == 'unpaid' ? 'selected' : '' }}>
                                Unpaid</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                            </option>
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="total-cost-display text-end p-3">
                        <small class="text-muted d-block mb-1">Total Amount Due</small>
                        <input type="hidden" id="total_price_input" name="total_amount"
                            value="{{ old('total_amount', 0) }}">
                        <span id="total-price-value" class="display-5 fw-bolder text-primary">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('transactions') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-1"></i>
                Submit Transaction</button>
        </div>
    </form>
</div>


@push('scripts')
    <script>
        // Ensure this script block runs only once per page load
        if (typeof window.transactionFormScriptLoaded === 'undefined') {
            window.transactionFormScriptLoaded = true;

            document.addEventListener("DOMContentLoaded", function() {
                let productIndex =
                    {{ old('products') ? count(old('products')) : (isset($transaction) && $transaction->transactionDetails->count() > 0 ? $transaction->transactionDetails->count() : 1) }};
                const productList = document.getElementById('product-list');
                const productsData = @json($products->keyBy('id'));
                const numberFormatter = new Intl.NumberFormat('id-ID');

                function updateTotal() {
                    let total = 0;
                    document.querySelectorAll('.product-item').forEach(function(row) {
                        const select = row.querySelector('.product-select');
                        const quantityInput = row.querySelector('.quantity-input');
                        const priceDisplay = row.querySelector('.price-display');
                        const selectedOption = select.selectedOptions[0];

                        const price = selectedOption && selectedOption.dataset.price ? parseFloat(
                            selectedOption.dataset.price) : 0;
                        const qty = parseInt(quantityInput.value) || 0;
                        const subtotal = price * qty;

                        if (priceDisplay) {
                            priceDisplay.value = isNaN(subtotal) ? '0' : numberFormatter.format(subtotal);
                        }
                        if (!isNaN(subtotal)) {
                            total += subtotal;
                        }
                    });
                    const totalPriceValueEl = document.getElementById('total-price-value');
                    if (totalPriceValueEl) {
                        totalPriceValueEl.textContent = 'Rp ' + numberFormatter.format(total);
                    }
                    const totalPriceInputEl = document.getElementById('total_price_input');
                    if (totalPriceInputEl) {
                        totalPriceInputEl.value = total;
                    }
                }

                function createProductRowHtml(index) {
                    let optionsHtml = '<option value="">-- Select Product --</option>';
                    for (const id in productsData) {
                        if (productsData.hasOwnProperty(id)) {
                            const product = productsData[id];
                            optionsHtml +=
                                `<option value="${product.id}" data-price="${product.selling_price}">${product.name} - Rp${numberFormatter.format(product.selling_price)}</option>`;
                        }
                    }

                    return `
                <div class="product-item raw-material-row mt-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                            <label class="form-label small d-lg-none">Product <span class="text-danger">*</span></label>
                            <select name="products[${index}][product_id]" class="form-select product-select" required>${optionsHtml}</select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-5 mb-2 mb-lg-0">
                            <label class="form-label small d-lg-none">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="products[${index}][quantity]" class="form-control quantity-input" min="1" value="1" required>
                        </div>
                        <div class="col-lg-3 col-md-5 col-sm-7 mb-2 mb-lg-0">
                            <label class="form-label small d-lg-none">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control price-display" readonly>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-12 text-lg-end">
                            <button type="button" class="btn btn-icon btn-danger remove-product" title="Remove Product"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
                }

                const addProductButton = document.getElementById('add-product');
                if (addProductButton) {
                    addProductButton.addEventListener('click', function() {
                        const newRowHtml = createProductRowHtml(productIndex);
                        productList.insertAdjacentHTML('beforeend', newRowHtml);
                        productIndex++;
                        updateTotal();
                    });
                }

                if (productList) {
                    productList.addEventListener('click', function(e) {
                        const removeButton = e.target.closest('.remove-product');
                        if (removeButton) {
                            const row = removeButton.closest('.product-item');
                            if (productList.querySelectorAll('.product-item').length > 1) {
                                row.remove();
                            } else {
                                const firstRow = productList.querySelector('.product-item');
                                if (firstRow) {
                                    firstRow.querySelector('.product-select').value = '';
                                    firstRow.querySelector('.quantity-input').value = '1';
                                }
                            }
                            updateTotal();
                        }
                    });

                    productList.addEventListener('change', function(e) {
                        if (e.target.classList.contains('product-select') || e.target.classList.contains(
                                'quantity-input')) {
                            updateTotal();
                        }
                    });
                    productList.addEventListener('input', function(e) {
                        if (e.target.classList.contains('quantity-input')) {
                            updateTotal();
                        }
                    });
                }

                updateTotal();
            });
        }
    </script>
@endpush
