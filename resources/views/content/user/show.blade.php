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
            <h5 class="section-title">{{ __('messages.supplier_and_contact_info') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">{{ __('messages.supplier_name') }} {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $supplier->name ?? '') }}"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="contact_person" class="form-label">{{ __('messages.contact_person') }} {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="contact_person" id="contact_person"
                        value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
                        class="form-control @error('contact_person') is-invalid @enderror" required>
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">{{ __('messages.email') }} {!! __('messages.required_field_indicator') !!}</label>
                    <input type="email" name="email" id="email"
                        value="{{ old('email', $supplier->email ?? '') }}"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">{{ __('messages.phone') }} {!! __('messages.required_field_indicator') !!}</label>
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
            <h5 class="section-title">{{ __('messages.address_details') }}</h5>
            <div class="row g-3">
                <div class="col-12">
                    <label for="address" class="form-label">{{ __('messages.full_address') }} {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="address" id="address"
                        value="{{ old('address', $supplier->address ?? '') }}"
                        class="form-control @error('address') is-invalid @enderror" required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="city" class="form-label">{{ __('messages.city') }}</label>
                    <input type="text" name="city" id="city"
                        value="{{ old('city', $supplier->city ?? '') }}"
                        class="form-control @error('city') is-invalid @enderror">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="state" class="form-label">{{ __('messages.state_province') }}</label>
                    <input type="text" name="state" id="state"
                        value="{{ old('state', $supplier->state ?? '') }}"
                        class="form-control @error('state') is-invalid @enderror">
                    @error('state')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="zip_code" class="form-label">{{ __('messages.zip_postal_code') }}</label>
                    <input type="text" name="zip_code" id="zip_code"
                        value="{{ old('zip_code', $supplier->zip_code ?? '') }}"
                        class="form-control @error('zip_code') is-invalid @enderror">
                    @error('zip_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="country" class="form-label">{{ __('messages.country') }}</label>
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
            <h5 class="section-title">{{ __('messages.status') }}</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="is_active_supplier" class="form-label">{{ __('messages.supplier_status') }}</label>
                    <select name="is_active" id="is_active_supplier"
                        class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1"
                            {{ old('is_active', $supplier->is_active ?? 1) == 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="0"
                            {{ old('is_active', $supplier->is_active ?? 1) == 0 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('suppliers') }}" class="btn btn-outline-secondary">{{ __('messages.cancel_button') }}</a>
            <button type="submit" class="btn btn-primary">{{ $isEdit ? __('messages.update_button') : __('messages.create_button') }} {{ __('messages.nav_suppliers') }}</button>
        </div>
    </form>
</div>