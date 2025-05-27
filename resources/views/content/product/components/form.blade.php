@php
    $isEdit = isset($product);
    $route = $isEdit ? route('products.update', $product->slug) : route('products.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="modern-form-container">
    <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method($method)
        @endif

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.product_details') }}</h5>
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">{{ __('messages.product_name') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $isEdit ? $product->name : '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="selling_price" class="form-label">{{ __('messages.selling_price') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="selling_price" id="selling_price" step="0.01" min="0"
                            class="form-control @error('selling_price') is-invalid @enderror"
                            value="{{ old('selling_price', $isEdit ? $product->selling_price : '') }}" required>
                    </div>
                    @error('selling_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="category_id" class="form-label">{{ __('messages.category') }}
                        {!! __('messages.required_field_indicator') !!}</label>
                    <select name="category_id" id="category_id"
                        class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">{{ __('messages.select_category_placeholder') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $isEdit ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="is_active" class="form-label">{{ __('messages.status') }}</label>
                    <select name="is_active" id="is_active"
                        class="form-select @error('is_active') is-invalid @enderror">
                        <option value="1"
                            {{ old('is_active', $isEdit ? $product->is_active : 1) == 1 ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="0"
                            {{ old('is_active', $isEdit ? $product->is_active : 1) == 0 ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.description') }} & {{ __('messages.image') }}</h5>
            <div class="row g-3">
                <div class="col-md-7">
                    <label for="description" class="form-label">{{ __('messages.description') }}</label>
                    <textarea name="description" id="description" rows="8"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-5">
                    <label for="image_path" class="form-label">{{ __('messages.product_image') }}</label>
                    <div class="image-upload-wrapper">
                        <input type="file" name="image_path" id="image_path" class="image-upload-input"
                            accept="image/*">
                        <div class="image-upload-placeholder">
                            <img id="image-preview"
                                src="{{ $isEdit && $product->image_path ? Storage::url($product->image_path) : '' }}"
                                alt="{{ __('messages.image_preview') }}"
                                class="{{ $isEdit && $product->image_path ? 'active' : '' }}">
                            <div class="placeholder-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>{{ __('messages.upload_image_instruction') }}</p>
                                <small>{{ __('messages.recommended_image_size') }}</small>
                            </div>
                        </div>
                    </div>
                    @error('image_path')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('products') }}" class="btn btn-outline-secondary">{{ __('messages.cancel_button') }}</a>
            <button type="submit"
                class="btn btn-primary">{{ $isEdit ? __('messages.update_button') : __('messages.create_button') }}
                {{ __('messages.nav_products') }}</button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image_path');
            const imagePreview = document.getElementById('image-preview');
            const placeholderContent = document.querySelector('.image-upload-placeholder .placeholder-content');

            if (imageInput) {
                imageInput.addEventListener('change', function(event) {
                    if (event.target.files && event.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.classList.add('active');
                            if (placeholderContent) placeholderContent.style.display = 'none';
                        }
                        reader.readAsDataURL(event.target.files[0]);
                    }
                });
                if (imagePreview.src && imagePreview.src !== window.location.href && imagePreview.src !== '') {
                    imagePreview.classList.add('active');
                    if (placeholderContent) placeholderContent.style.display = 'none';
                }
            }
        });
    </script>
@endpush
