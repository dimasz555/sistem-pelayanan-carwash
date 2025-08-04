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
                data-service-id="{{ $service->id }}" data-service-price="{{ $service->price }}"
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
        // console.log('Updating service selection:', serviceId, servicePrice);

        // Update service_id field
        updateFieldValue('service_id', serviceId);

        // Update service_price field  
        updateFieldValue('service_price', servicePrice);

        // Small delay to ensure fields are updated before calculation
        setTimeout(() => {
            recalculateTotal();
        }, 50);
    }

    function updateFieldValue(fieldName, value) {
        let field = null;
        let updated = false;

        // Method 1: Try to find field with various selectors
        const selectors = [
            `input[name="${fieldName}"]`,
            `input[data-field-name="${fieldName}"]`,
            `input[wire\\:model="data.${fieldName}"]`,
            `input[wire\\:model="${fieldName}"]`,
            `input[x-model="state.${fieldName}"]`,
            `input[x-model="${fieldName}"]`,
            `[data-field-wrapper="${fieldName}"] input`,
            `.fi-fo-field-wrp[data-field-name="${fieldName}"] input`
        ];

        for (const selector of selectors) {
            field = document.querySelector(selector);
            if (field) {
                // console.log(`Found field ${fieldName} with selector: ${selector}`);
                break;
            }
        }

        if (field) {
            const oldValue = field.value;
            field.value = value;

            // Trigger events for Filament/Livewire
            ['input', 'change', 'blur'].forEach(eventType => {
                field.dispatchEvent(new Event(eventType, {
                    bubbles: true,
                    cancelable: true
                }));
            });

            // For Alpine.js
            if (field._x_model) {
                field._x_model.set(value);
            }

            // Livewire specific update
            if (window.Livewire && field.hasAttribute('wire:model')) {
                const wireModel = field.getAttribute('wire:model');
                try {
                    window.Livewire.find(field.closest('[wire\\:id]').getAttribute('wire:id')).set(wireModel, value);
                } catch (e) {
                    console.warn('Livewire update failed:', e);
                }
            }

            // console.log(`Updated ${fieldName}: ${oldValue} -> ${value}`);
            updated = true;
        } else {
            console.warn(`Field ${fieldName} not found with any selector`);
        }

        return updated;
    }

    function getFieldValue(fieldName) {
        const selectors = [
            `input[name="${fieldName}"]`,
            `input[data-field-name="${fieldName}"]`,
            `input[wire\\:model="data.${fieldName}"]`,
            `input[wire\\:model="${fieldName}"]`,
            `input[x-model="state.${fieldName}"]`,
            `input[x-model="${fieldName}"]`,
            `[data-field-wrapper="${fieldName}"] input`,
            `.fi-fo-field-wrp[data-field-name="${fieldName}"] input`
        ];

        for (const selector of selectors) {
            const field = document.querySelector(selector);
            if (field) {
                const value = parseFloat(field.value) || 0;
                // console.log(`Got ${fieldName} value: ${value}`);
                return value;
            }
        }

        // console.warn(`Could not get value for field: ${fieldName}`);
        return 0;
    }

    function recalculateTotal() {
        const servicePrice = getFieldValue('service_price');
        const discountAmount = getFieldValue('discount_amount');
        const totalPrice = Math.max(0, servicePrice - discountAmount);

        // console.log('Recalculating total:', {
        //     servicePrice,
        //     discountAmount,
        //     totalPrice
        // });

        updateFieldValue('total_price', totalPrice);

        // Force update of total display
        setTimeout(() => {
            triggerTotalDisplayUpdate();
        }, 100);
    }

    function triggerTotalDisplayUpdate() {
        // Try to trigger reactive update for total display
        const totalField = document.querySelector('input[name="total_price"]') ||
            document.querySelector('input[data-field-name="total_price"]');

        if (totalField) {
            // Trigger custom event for Filament reactive fields
            totalField.dispatchEvent(new CustomEvent('filament-field-updated', {
                bubbles: true,
                detail: {
                    value: totalField.value
                }
            }));
        }

        // Also try to find and update any reactive placeholders
        const placeholders = document.querySelectorAll('[wire\\:key*="total"], [x-data*="total"]');
        placeholders.forEach(placeholder => {
            if (placeholder.__x) {
                placeholder.__x.updateElements();
            }
        });
    }

    // Initialize on page load for edit mode
    document.addEventListener('DOMContentLoaded', function() {
        // console.log('Service selector loaded');

        // Set up initial values
        const checkedRadio = document.querySelector('input[name="service_selection"]:checked');
        if (checkedRadio) {
            const serviceId = checkedRadio.dataset.serviceId;
            const servicePrice = checkedRadio.dataset.servicePrice;
            // console.log('Found checked service:', serviceId, servicePrice);
            updateServiceSelection(parseInt(serviceId), parseInt(servicePrice));
        }

        // Enhanced event listener for discount changes
        document.addEventListener('input', function(e) {
            const target = e.target;

            // Check if this is a discount field
            if (target.name === 'discount_amount' ||
                target.dataset.fieldName === 'discount_amount' ||
                target.getAttribute('wire:model')?.includes('discount_amount')) {

                console.log('Discount changed, recalculating...');
                setTimeout(() => {
                    recalculateTotal();
                }, 50);
            }
        });

        // Listen for any field changes that might affect calculation
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' &&
                    (mutation.attributeName === 'value' || mutation.attributeName ===
                        'wire:model')) {
                    const target = mutation.target;
                    if (target.name === 'service_price' || target.dataset.fieldName ===
                        'service_price') {
                        // console.log('Service price field mutated, recalculating...');
                        setTimeout(() => {
                            recalculateTotal();
                        }, 100);
                    }
                }
            });
        });

        // Observe form changes
        const form = document.querySelector('form');
        if (form) {
            observer.observe(form, {
                subtree: true,
                attributes: true,
                attributeFilter: ['value', 'wire:model']
            });
        }
    });

    // // Debug function to check field states
    // window.debugServiceSelector = function() {
    //     console.log('=== Service Selector Debug ===');
    //     console.log('Service ID:', getFieldValue('service_id'));
    //     console.log('Service Price:', getFieldValue('service_price'));
    //     console.log('Discount Amount:', getFieldValue('discount_amount'));
    //     console.log('Total Price:', getFieldValue('total_price'));

    //     const checkedRadio = document.querySelector('input[name="service_selection"]:checked');
    //     if (checkedRadio) {
    //         console.log('Selected service radio:', {
    //             id: checkedRadio.dataset.serviceId,
    //             price: checkedRadio.dataset.servicePrice
    //         });
    //     }

    //     console.log('=== End Debug ===');
    // };
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
