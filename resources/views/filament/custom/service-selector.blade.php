@php
    use App\Models\Service;
    $services = Service::all();

    // Get current record for edit mode
    $currentRecord = null;
    if (isset($getRecord) && is_callable($getRecord)) {
        $currentRecord = $getRecord();
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($services as $service)
        <label
            class="flex items-start p-4 border-2 rounded-lg shadow-sm cursor-pointer hover:shadow-md hover:border-primary-300 transition-all duration-200 group">
            <input type="radio" name="service_selection" value="{{ $service->id }}"
                class="mt-2 mr-4 scale-125 text-primary-600 focus:ring-primary-500 service-radio"
                onchange="updateServiceSelection({{ $service->id }}, {{ $service->price }})"
                @if ($currentRecord && $currentRecord->service_id == $service->id) checked @endif required>

            <div class="flex items-start gap-4 px-2 flex-1">
                <!-- Thumbnail Image -->
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $service->thumbnail) }}" alt="{{ $service->name }}"
                        class="w-20 h-20 object-cover rounded-lg border">
                </div>

                <!-- Service Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700 transition-colors">
                            {{ $service->name }}
                        </h3>
                        <span class="text-lg font-bold text-primary-600 whitespace-nowrap ml-4">
                            Rp {{ number_format($service->price, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($service->description)
                        <p class="text-sm text-gray-600 line-clamp-2 mb-2">
                            {{ $service->description }}
                        </p>
                    @endif

                    <!-- Additional service info if available -->
                    <div class="flex flex-wrap gap-2 text-xs">
                        @if ($service->category)
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                {{ $service->category->name ?? 'Kategori' }}
                            </span>
                        @endif

                        @if ($service->size)
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                    </path>
                                </svg>
                                {{ $service->size->name ?? 'Ukuran' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </label>
    @endforeach
</div>

<script>
    function updateServiceSelection(serviceId, servicePrice) {
        // Update service_id field
        updateFieldValue('service_id', serviceId);

        // Update service_price field  
        updateFieldValue('service_price', servicePrice);

        // Recalculate total price with current discount
        recalculateTotal();
    }

    function updateFieldValue(fieldName, value) {
        // Method 1: Try data-field-name attribute
        let field = document.querySelector(`input[data-field-name="${fieldName}"]`);

        // Method 2: Try wire:model attribute
        if (!field) {
            field = document.querySelector(`input[wire\\:model="data.${fieldName}"]`);
        }

        // Method 3: Try name attribute
        if (!field) {
            field = document.querySelector(`input[name="${fieldName}"]`);
        }

        // Method 4: Try Alpine.js x-model
        if (!field) {
            field = document.querySelector(`input[x-model="state.${fieldName}"]`);
        }

        if (field) {
            field.value = value;
            // Trigger multiple events to ensure Filament detects the change
            field.dispatchEvent(new Event('input', {
                bubbles: true
            }));
            field.dispatchEvent(new Event('change', {
                bubbles: true
            }));

            // For Alpine.js
            if (field._x_model) {
                field._x_model.set(value);
            }
        } else {
            console.warn(`Field ${fieldName} not found`);
        }
    }

    function getFieldValue(fieldName) {
        let field = document.querySelector(`input[data-field-name="${fieldName}"]`) ||
            document.querySelector(`input[wire\\:model="data.${fieldName}"]`) ||
            document.querySelector(`input[name="${fieldName}"]`) ||
            document.querySelector(`input[x-model="state.${fieldName}"]`);

        return field ? parseFloat(field.value) || 0 : 0;
    }

    function recalculateTotal() {
        const servicePrice = getFieldValue('service_price');
        const discountAmount = getFieldValue('discount_amount');
        const totalPrice = Math.max(0, servicePrice - discountAmount);

        updateFieldValue('total_price', totalPrice);

        // Update visual feedback if discount percentage display exists
        updateDiscountPercentage(servicePrice, discountAmount);
    }

    function updateDiscountPercentage(servicePrice, discountAmount) {
        const percentageElement = document.querySelector('#discount-percentage');
        if (percentageElement && servicePrice > 0) {
            const percentage = ((discountAmount / servicePrice) * 100).toFixed(1);
            percentageElement.textContent = `Diskon: ${percentage}%`;
        }
    }

    // Initialize on page load for edit mode
    document.addEventListener('DOMContentLoaded', function() {
        const checkedRadio = document.querySelector('input[name="service_selection"]:checked');
        if (checkedRadio) {
            const serviceId = checkedRadio.value;
            // Find the price from the radio button's parent elements
            const priceElement = checkedRadio.closest('label').querySelector('.text-primary-600');
            if (priceElement) {
                const priceText = priceElement.textContent.replace(/[^\d]/g, '');
                const servicePrice = parseInt(priceText);
                updateServiceSelection(serviceId, servicePrice);
            }
        }
    });

    // Listen for discount amount changes to recalculate total
    document.addEventListener('input', function(e) {
        if (e.target.dataset.fieldName === 'discount_amount' ||
            e.target.name === 'discount_amount' ||
            e.target.getAttribute('wire:model') === 'data.discount_amount') {
            recalculateTotal();
        }
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
