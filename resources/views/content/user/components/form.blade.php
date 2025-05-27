@php
    $isEdit = isset($user) && $user->exists;
    $route = $isEdit ? route('users.update', $user->id) : route('users.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="modern-form-container">
    <form action="{{ $route }}" method="POST">
        @csrf
        @if ($isEdit)
            @method($method)
        @endif

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.user_information') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">{{ __('messages.name') }} {!! __('messages.required_field_indicator') !!}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">{{ __('messages.email') }} {!! __('messages.required_field_indicator') !!}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}"
                        class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="role_id" class="form-label">{{ __('messages.role') }} {!! __('messages.required_field_indicator') !!}</label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror"
                        required>
                        <option value="">{{ __('messages.select_role_placeholder') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                {{ Str::title($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5 class="section-title">{{ __('messages.password') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">{{ __('messages.password') }} @if (!$isEdit)
                            {!! __('messages.required_field_indicator') !!}
                        @endif
                    </label>
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        @if (!$isEdit) required @endif>
                    @if ($isEdit)
                        <div class="form-text">{{ __('messages.password_leave_blank_info') }}</div>
                    @endif
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">{{ __('messages.confirm_password') }}
                        @if (!$isEdit)
                            {!! __('messages.required_field_indicator') !!}
                        @endif
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        @if (!$isEdit) required @endif>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('users') }}" class="btn btn-outline-secondary">{{ __('messages.cancel_button') }}</a>
            <button type="submit"
                class="btn btn-primary">{{ $isEdit ? __('messages.update_button') : __('messages.create_button') }}
                {{ __('messages.nav_users') }}</button>
        </div>
    </form>
</div>
