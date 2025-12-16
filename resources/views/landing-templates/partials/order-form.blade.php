<!-- Order Form -->
@if(!isset($hideSection) || !$hideSection)
<section class="order-form">
    <div class="container">
        <h2>{{ app()->getLocale() === 'ar' ? 'اطلب الآن' : 'Order Now' }}</h2>

        @if($errors->any())
        <div class="error-message" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>{{ app()->getLocale() === 'ar' ? 'خطأ:' : 'Error:' }}</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="success-message" style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
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
                <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }} *</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required>
                @error('customer_phone')
                <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }} *</label>
                <textarea name="customer_address" required>{{ old('customer_address') }}</textarea>
                @error('customer_address')
                <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
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
                <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
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
                    <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
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
                                <div class="variation-color-box" style="background-color: {{ trim($color['value'] ?? '') }};"></div>
                                @elseif($color['isImageUrl'] ?? false)
                                <div class="variation-image">
                                    <img src="{{ $color['value'] ?? '' }}" alt="{{ $color['name'] ?? '' }}" onerror="this.parentElement.style.display='none'">
                                </div>
                                @endif
                                <span class="variation-name">{{ $color['name'] ?? '' }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('product_color')
                    <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <!-- Quantity Field (Always Required) -->
                <div class="form-group">
                    <label>{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }} *</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                    @error('quantity')
                    <span class="error-message" style="color: #dc2626; font-size: 14px; margin-top: 5px; display: block;">{{ $message }}</span>
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

            <button type="submit" class="btn-submit">
                {{ app()->getLocale() === 'ar' ? 'تأكيد الطلب' : 'Place Order' }}
            </button>
        </form>
    </div>
</section>
@endif

<style>
    /* Product Variations Styles */
    .product-variations {
        margin: 20px 0;
    }

    .variation-group {
        margin-bottom: 25px;
    }

    .variation-group label {
        display: block;
        margin-bottom: 12px;
        font-weight: 600;
        color: #374151;
        font-size: 16px;
    }

    .variation-options {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .variation-option {
        position: relative;
        cursor: pointer;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.3s;
        background: white;
        min-width: 120px;
        text-align: center;
    }

    .variation-option:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .variation-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .variation-option input[type="radio"]:checked+.variation-content {
        color: #667eea;
        font-weight: 600;
    }

    .variation-option input[type="radio"]:checked~.variation-content,
    .variation-option:has(input[type="radio"]:checked) {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .variation-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .variation-image {
        width: 80px;
        height: 80px;
        border-radius: 6px;
        overflow: hidden;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .variation-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .variation-color-box {
        width: 80px;
        height: 80px;
        border-radius: 6px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .variation-name {
        font-size: 14px;
        color: #374151;
    }

    .variation-option input[type="radio"]:checked~.variation-content .variation-name,
    .variation-option:has(input[type="radio"]:checked) .variation-name {
        color: #667eea;
        font-weight: 600;
    }

    /* Quantity Field */
    .form-group input[type="number"][name="quantity"] {
        width: 100%;
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    .form-group input[type="number"][name="quantity"]:focus {
        outline: none;
        border-color: #667eea;
    }

    @media (max-width: 640px) {
        .variation-options {
            gap: 8px;
        }

        .variation-option {
            min-width: 100px;
            padding: 10px;
        }

        .variation-image {
            width: 60px;
            height: 60px;
        }
    }
</style>