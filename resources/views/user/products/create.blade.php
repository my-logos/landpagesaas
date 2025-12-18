@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="product-form-container">
        <div class="product-form-header">
            <button type="button" class="close-btn">
                <i class="fa-solid fa-times"></i>
            </button>
            <div class="product-form-title-group">
                <h1 class="page-title">{{ $t('messages.control_panel') ?? 'Control Panel' }}</h1>
                <h2 class="product-form-title">{{ isset($product) ? ($t('messages.edit_product') ?? 'Edit Product') : ($t('messages.add_new_product') ?? 'Add New Product') }}</h2>
            </div>
        </div>

        @if ($errors->any())
        <div data-flash-message="{{ $errors->first() }}" data-flash-type="error"></div>
        @endif

        <form method="POST" action="{{ isset($product) ? route('user.products.update', $product) : route('user.products.store') }}" enctype="multipart/form-data" class="product-form">
            @csrf
            @if(isset($product))
            @method('PUT')
            @endif

            <div class="product-form-card">
                <!-- Product Name -->
                <div class="form-group">
                    <label for="name" class="form-label">{{ $t('messages.product_name') ?? 'Product Name' }} *</label>
                    <div class="input-with-counter">
                        <input type="text" id="name" name="name" class="form-control"
                            value="{{ old('name', $product->name ?? '') }}"
                            placeholder="{{ $t('messages.enter_product_name') ?? 'Enter product name' }}"
                            required minlength="10" maxlength="250">
                        <span class="char-counter" id="name-counter">0/250</span>
                    </div>
                    <small class="form-help">{{ $t('messages.product_name_help') ?? 'Enter a clear and distinctive name for the product (10-250 characters)' }}</small>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- Short Description -->
                <div class="form-group">
                    <label for="short_description" class="form-label">{{ $t('messages.short_product_description') ?? 'Short Product Description' }} *</label>
                    <div class="input-with-counter">
                        <input type="text" id="short_description" name="short_description" class="form-control"
                            value="{{ old('short_description', $product->short_description ?? '') }}"
                            placeholder="{{ $t('messages.add_short_product_description') ?? 'Add short product description' }}"
                            required minlength="10" maxlength="250">
                        <span class="char-counter" id="short_description-counter">0/250</span>
                    </div>
                    <small class="form-help">{{ $t('messages.short_description_help') ?? 'Add a short and useful description for the product' }}</small>
                    @error('short_description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- Product Price -->
                <div class="form-group">
                    <label for="price" class="form-label">{{ $t('messages.product_price') ?? 'Product Price' }} *</label>
                    <input type="number" id="price" name="price_cents" class="form-control"
                        value="{{ old('price_cents', isset($product) ? ($product->price_cents / 100) : '') }}"
                        placeholder="{{ $t('messages.enter_product_price') ?? 'Enter product price' }}"
                        step="0.01" min="0" required>
                    <small class="form-help">{{ $t('messages.product_price_help') ?? 'Product price in English numbers' }}</small>
                    @error('price_cents')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- Shipping Price -->
                <div class="form-group">
                    <label for="shipping_price" class="form-label">{{ $t('messages.shipping_price') ?? 'Shipping Price' }}</label>
                    <input type="number" id="shipping_price" name="shipping_price_cents" class="form-control"
                        value="{{ old('shipping_price_cents', isset($product) ? ($product->shipping_price_cents / 100) : 0) }}"
                        step="0.01" min="0">
                    <small class="form-help">{{ $t('messages.shipping_price_help') ?? 'Default value: 0 (free shipping)' }}</small>
                    @error('shipping_price_cents')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- Currency -->
                <div class="form-group">
                    <label for="currency" class="form-label">{{ $t('messages.currency') ?? 'Currency' }} *</label>
                    <select id="currency" name="currency" class="form-control" required>
                        <option value="">{{ $t('messages.select_currency') ?? 'Select Currency' }}</option>
                        @foreach($currencies as $code => $name)
                        <option value="{{ $code }}" {{ (old('currency', isset($product) ? $product->currency : $defaultCurrency) === $code) ? 'selected' : '' }}>{{ $name }} ({{ $code }})</option>
                        @endforeach
                    </select>
                    <small class="form-help">{{ $t('messages.select_product_currency') ?? 'Select product currency' }}</small>
                    @error('currency')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- AI Version Section -->
                <div class="form-group">
                    <label for="ai_version" class="form-label">{{ $t('messages.ai_version') ?? 'إصدار الذكاء الاصطناعي' }}</label>
                    <select id="ai_version" name="ai_version" class="form-control" required>
                        @foreach($availableProviders as $provider => $info)
                        <option value="{{ $provider }}"
                            {{ old('ai_version', isset($product) ? $product->ai_version : $defaultProvider) == $provider ? 'selected' : '' }}
                            @if($info['is_free']==false && isset($isFreePlan) && $isFreePlan) disabled @endif>
                            {{ $info['name'] }}
                            @if($info['is_free'])
                            ({{ $t('messages.free') ?? 'مجاني' }})
                            @else
                            ({{ $t('messages.paid') ?? 'مدفوع' }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <small class="form-help">
                        @if(isset($availableProviders[$defaultProvider]))
                        {{ $t('messages.ai_version_description') ?? 'اختر إصدار الذكاء الاصطناعي لاستخدامه في إنشاء محتوى المنتج' }}
                        @else
                        {{ $t('messages.ai_version_default_only') ?? 'الإصدار الافتراضي فقط متاح' }}
                        @endif
                    </small>
                    @if(isset($isFreePlan) && $isFreePlan)
                    <div class="form-warning">
                        <i class="fa-solid fa-lock"></i>
                        {{ $t('messages.paid_ai_available_for_paid_plans') ?? 'الإصدارات المدفوعة متاحة فقط للباقات المدفوعة' }}
                    </div>
                    @endif
                    @error('ai_version')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <!-- Product Images -->
                <div class="form-group">
                    <label class="form-label">{{ $t('messages.product_images') ?? 'Product Images' }}</label>
                    <div class="image-upload-required">
                        {{ $t('messages.product_images_required') ?? 'يجب إضافة من 1 إلى 6 صور للمنتج' }}
                    </div>
                    <div class="image-upload-actions">
                        <button type="button" class="btn-upload-device" id="upload-device-btn">
                            <i class="fa-solid fa-upload"></i>
                            {{ $t('messages.upload_from_device') ?? 'Upload from Device' }}
                        </button>
                        <button type="button" class="btn-add-image-link" id="add-image-link-btn">
                            <i class="fa-solid fa-plus"></i>
                            {{ $t('messages.add_image_link') ?? 'Image Link' }}
                        </button>
                    </div>
                    <input type="file" id="image_files" name="image_files[]" multiple accept="image/*">
                    <div class="image-upload-area" id="drop-zone">
                        <i class="fa-solid fa-upload"></i>
                        <p>{{ $t('messages.drag_drop_images') ?? 'Drag and drop images here or click to select' }}</p>
                    </div>
                    <div class="image-preview-container" id="image-preview-container"></div>
                    <input type="hidden" name="images" id="image-urls" value="{{ isset($product) && $product->images ? json_encode($product->images) : '' }}">
                    @error('images')<span class="form-error">{{ $message }}</span>@enderror
                    @error('image_files')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions visible">
                <button type="button" class="btn">
                    {{ $t('messages.cancel') ?? 'Cancel' }}
                </button>
                <button type="submit" class="btn">
                    {{ $t('messages.save_product') ?? 'Save Product' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/product-form.js') }}"></script>
@endpush

@if(isset($product) && $product->images)
<div id="product-images-data" data-images="{{ json_encode($product->images) }}" class="hidden"></div>
@endif
@endsection