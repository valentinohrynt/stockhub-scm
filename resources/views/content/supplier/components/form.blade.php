@php
    $isEdit = isset($supplier) && $supplier->exists; // Check if $supplier exists and is an actual model instance
    $route = $isEdit ? route('suppliers.update', $supplier->slug) : route('suppliers.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="modern-form-container">
    <form action="{{ $route }}" method="POST">
        @csrf
        @if ($isEdit)
            @method($method)
        @endif

        <div class="form-section">
            <h5 class="section-title">Supplier & Contact Information</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $supplier->name ?? '') }}"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="contact_person" class="form-label">Contact Person <span
                            class="text-danger">*</span></label>
                    <input type="text" name="contact_person" id="contact_person"
                        value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                        class="form-control @error('contact_person') is-invalid @enderror" required>
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                        value="{{ old('email', $supplier->email ?? '') }}"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone"
                        value="{{ old('phone', $supplier->phone ?? '') }}"
                        class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">Address Details</h5>
            <div class="row g-3">
                <div class="col-12">
                    <label for="address" class="form-label">Full Address <span class="text-danger">*</span></label>
                    <input type="text" name="address" id="address"
                        value="{{ old('address', $supplier->address ?? '') }}"
                        class="form-control @error('address') is-invalid @enderror" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" name="city" id="city"
                        value="{{ old('city', $supplier->city ?? '') }}"
                        class="form-control @error('city') is-invalid @enderror">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="state" class="form-label">State / Province</label>
                    <input type="text" name="state" id="state"
                        value="{{ old('state', $supplier->state ?? '') }}"
                        class="form-control @error('state') is-invalid @enderror">
                    @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="zip_code" class="form-label">Zip / Postal Code</label>
                    <input type="text" name="zip_code" id="zip_code"
                        value="{{ old('zip_code', $supplier->zip_code ?? '') }}"
                        class="form-control @error('zip_code') is-invalid @enderror">
                    @error('zip_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" name="country" id="country"
                        value="{{ old('country', $supplier->country ?? '') }}"
                        class="form-control @error('country') is-invalid @enderror">
                    @error('country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">Status</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="is_active_supplier" class="form-label">Supplier Status</label>
                    <select name="is_active" id="is_active_supplier"
                        class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1"
                            {{ old('is_active', $supplier->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0"
                            {{ old('is_active', $supplier->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('suppliers') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }} Supplier</button>
        </div>
    </form>
</div>
