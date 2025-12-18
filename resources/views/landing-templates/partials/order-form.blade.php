<!-- Order Form -->
@if(!isset($hideSection) || !$hideSection)
<section class="order-form">
    <div class="container">
        <h2>{{ app()->getLocale() === 'ar' ? 'اطلب الآن' : 'Order Now' }}</h2>

        @if($errors->any())
        <div class="error-message">
            <strong>{{ app()->getLocale() === 'ar' ? 'خطأ:' : 'Error:' }}</strong>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
        @endif

        <form id="orderForm" action="{{ route('page.order.store', $page->id) }}" method="POST">
            @csrf
            @if($page->product)
            <input type="hidden" name="product_id" value="{{ $page->product->id }}">
            @else
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'اختر المنتج' : 'Select Product' }} *</label>
                <select name="product_id" required>
                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر منتجاً' : 'Select a product' }}</option>
                    @foreach(\App\Models\Product::where('user_id', $page->user_id)->get() as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($page->form_type === 'default')
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }} *</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required>
                @error('customer_name')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }} *</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required>
                @error('customer_phone')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }} *</label>
                <textarea name="customer_address" required>{{ old('customer_address') }}</textarea>
                @error('customer_address')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            @else
            @if($page->form_fields && is_array($page->form_fields) && count($page->form_fields) > 0)
            @foreach($page->form_fields as $field)
            @if($field !== 'quantity') {{-- Quantity is handled separately --}}
            <div class="form-group">
                <label>{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }} *</label>
                @if($field === 'address' || $field === 'notes')
                <textarea name="form_data[{{ $field }}]" required>{{ old('form_data.' . $field) }}</textarea>
                @elseif($field === 'email')
                <input type="email" name="form_data[{{ $field }}]" value="{{ old('form_data.' . $field) }}" required>
                @else
                <input type="text" name="form_data[{{ $field }}]" value="{{ old('form_data.' . $field) }}" required>
                @endif
                @error('form_data.' . $field)
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            @endif
            @endforeach
            @endif
            @endif


            @if(!empty($sizes) || !empty($colors))
            <!-- Product Variations & Quantity (Before Submit Button) -->
            <div class="product-variations">
                @if(!empty($sizes))
                <div class="form-group variation-group">
                    <label>{{ app()->getLocale() === 'ar' ? 'اختر المقاس' : 'Select Size' }} *</label>
                    <div class="variation-options">
                        @foreach($sizes as $index => $size)
                        <label class="variation-option">
                            <input type="radio" name="product_size" value="{{ $size['name'] ?? '' }}" {{ $index === 0 ? 'required' : '' }}>
                            <div class="variation-content">
                                <span class="variation-name">{{ $size['name'] ?? '' }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('product_size')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                @if(!empty($processedColors))
                <div class="form-group variation-group">
                    <label>{{ app()->getLocale() === 'ar' ? 'اختر اللون' : 'Select Color' }} *</label>
                    <div class="variation-options">
                        @foreach($processedColors as $index => $color)
                        <label class="variation-option">
                            <input type="radio" name="product_color" value="{{ $color['name'] ?? '' }}" {{ $index === 0 ? 'required' : '' }}>
                            <div class="variation-content">
                                @if($color['isHexColor'] ?? false)
                                <div class="variation-color-box" data-color="{{ trim($color['value'] ?? '') }}"></div>
                                @elseif($color['isImageUrl'] ?? false)
                                <div class="variation-image">
                                    <img src="{{ $color['value'] ?? '' }}" alt="{{ $color['name'] ?? '' }}" data-hide-on-error>
                                </div>
                                @endif
                                <span class="variation-name">{{ $color['name'] ?? '' }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('product_color')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <!-- Quantity Field (Always Required) -->
                <div class="form-group">
                    <label>{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }} *</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                    @error('quantity')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @else
            <!-- Quantity Field Only (if no variations) -->
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }} *</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
            </div>
            @endif

            @include('landing-templates.partials.recaptcha')

            <button type="submit" class="btn-submit">
                {{ app()->getLocale() === 'ar' ? 'تأكيد الطلب' : 'Place Order' }}
            </button>
        </form>
    </div>
</section>
@endif