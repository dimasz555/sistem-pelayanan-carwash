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
        console.log('Updating service selection:', serviceId, servicePrice);

        // Update service_id field
        updateFieldValue('service_id', serviceId);

        // Update service_price field  
        updateFieldValue('service_price', servicePrice);

        // Reset promo when service changes (only if not in edit mode)
        const isEditMode = document.querySelector('input[name="service_selection"]:checked')?.hasAttribute(
            'data-edit-mode');
        if (!isEditMode) {
            resetPromo();
        }

        // Small delay to ensure fields are updated before calculation
        setTimeout(() => {
            recalculateTotal();
            triggerFilamentUpdate();
        }, 100);
    }

    function resetPromo() {
        // Reset promo selection when service changes
        updateFieldValue('promo_id', '');
        updateFieldValue('promo_discount', 0);

        // Clear promo select dropdown
        const promoSelect = document.querySelector('select[name="promo_id"]') ||
            document.querySelector('[data-field-name="promo_id"] select');
        if (promoSelect) {
            promoSelect.value = '';
            promoSelect.dispatchEvent(new Event('change', {
                bubbles: true
            }));
        }
    }

    function updateFieldValue(fieldName, value) {
        let field = null;
        let updated = false;

        // Method 1: Try to find field with various selectors
        const selectors = [
            `input[name="${fieldName}"]`,
            `select[name="${fieldName}"]`,
            `input[data-field-name="${fieldName}"]`,
            `select[data-field-name="${fieldName}"]`,
            `input[wire\\:model="data.${fieldName}"]`,
            `select[wire\\:model="data.${fieldName}"]`,
            `input[wire\\:model="${fieldName}"]`,
            `select[wire\\:model="${fieldName}"]`,
            `input[x-model="state.${fieldName}"]`,
            `select[x-model="state.${fieldName}"]`,
            `input[x-model="${fieldName}"]`,
            `select[x-model="${fieldName}"]`,
            `[data-field-wrapper="${fieldName}"] input`,
            `[data-field-wrapper="${fieldName}"] select`,
            `.fi-fo-field-wrp[data-field-name="${fieldName}"] input`,
            `.fi-fo-field-wrp[data-field-name="${fieldName}"] select`
        ];

        for (const selector of selectors) {
            field = document.querySelector(selector);
            if (field) {
                console.log(`Found field ${fieldName} with selector: ${selector}`);
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
                    const livewireComponent = field.closest('[wire\\:id]');
                    if (livewireComponent) {
                        const componentId = livewireComponent.getAttribute('wire:id');
                        window.Livewire.find(componentId).set(wireModel, value);
                    }
                } catch (e) {
                    console.warn('Livewire update failed:', e);
                }
            }

            console.log(`Updated ${fieldName}: ${oldValue} -> ${value}`);
            updated = true;
        } else {
            console.warn(`Field ${fieldName} not found with any selector`);
        }

        return updated;
    }

    function getFieldValue(fieldName) {
        const selectors = [
            `input[name="${fieldName}"]`,
            `select[name="${fieldName}"]`,
            `input[data-field-name="${fieldName}"]`,
            `select[data-field-name="${fieldName}"]`,
            `input[wire\\:model="data.${fieldName}"]`,
            `select[wire\\:model="data.${fieldName}"]`,
            `input[wire\\:model="${fieldName}"]`,
            `select[wire\\:model="${fieldName}"]`,
            `input[x-model="state.${fieldName}"]`,
            `select[x-model="state.${fieldName}"]`,
            `input[x-model="${fieldName}"]`,
            `select[x-model="${fieldName}"]`,
            `[data-field-wrapper="${fieldName}"] input`,
            `[data-field-wrapper="${fieldName}"] select`,
            `.fi-fo-field-wrp[data-field-name="${fieldName}"] input`,
            `.fi-fo-field-wrp[data-field-name="${fieldName}"] select`
        ];

        for (const selector of selectors) {
            const field = document.querySelector(selector);
            if (field) {
                // Handle different field types
                if (field.type === 'checkbox') {
                    const value = field.checked ? 1 : 0;
                    console.log(`Got ${fieldName} value (checkbox): ${value}`);
                    return value;
                } else if (field.tagName.toLowerCase() === 'select') {
                    const value = field.value || '';
                    console.log(`Got ${fieldName} value (select): ${value}`);
                    return fieldName.includes('_id') ? value : (parseFloat(value) || 0);
                } else {
                    const value = parseFloat(field.value) || 0;
                    console.log(`Got ${fieldName} value: ${value}`);
                    return value;
                }
            }
        }

        // Return default values for specific fields if not found
        const defaultValues = {
            'is_free': 0,
            'promo_discount': 0,
            'service_price': 0,
            'total_price': 0,
            'promo_id': ''
        };

        const defaultValue = defaultValues[fieldName] !== undefined ? defaultValues[fieldName] : 0;

        // Only warn for critical fields, not optional ones
        if (!['is_free', 'promo_discount'].includes(fieldName)) {
            console.warn(`Could not get value for field: ${fieldName}, using default: ${defaultValue}`);
        } else {
            console.log(`Field ${fieldName} not found, using default: ${defaultValue}`);
        }

        return defaultValue;
    }

    function recalculateTotal() {
        const servicePrice = getFieldValue('service_price');
        const promoDiscount = getFieldValue('promo_discount');
        const isFree = getFieldValue('is_free');

        let totalPrice = 0;

        if (isFree === 1 || isFree === true) {
            totalPrice = 0;
        } else {
            totalPrice = Math.max(0, servicePrice - promoDiscount);
        }

        console.log('Recalculating total:', {
            servicePrice,
            promoDiscount,
            isFree,
            totalPrice
        });

        // Only update total_price if it's different from current value
        const currentTotal = getFieldValue('total_price');
        if (currentTotal !== totalPrice) {
            updateFieldValue('total_price', totalPrice);
        }
    }

    function triggerFilamentUpdate() {
        // Trigger Filament/Livewire to update reactive components
        const event = new CustomEvent('service-selected', {
            bubbles: true,
            detail: {
                timestamp: Date.now()
            }
        });
        document.dispatchEvent(event);

        // Also try to trigger any wire:model updates
        const wireModelFields = document.querySelectorAll('[wire\\:model]');
        wireModelFields.forEach(field => {
            if (field.name === 'service_id' || field.name === 'service_price' || field.name === 'total_price') {
                field.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }
        });
    }

    // Initialize on page load for edit mode
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Service selector loaded');

        // Set up initial values
        const checkedRadio = document.querySelector('input[name="service_selection"]:checked');
        if (checkedRadio) {
            const serviceId = checkedRadio.dataset.serviceId;
            const servicePrice = checkedRadio.dataset.servicePrice;
            console.log('Found checked service:', serviceId, servicePrice);

            // Don't reset promo on initial load (for edit mode)
            updateFieldValue('service_id', parseInt(serviceId));
            updateFieldValue('service_price', parseInt(servicePrice));

            setTimeout(() => {
                recalculateTotal();
                triggerFilamentUpdate();
            }, 200);
        }

        // Enhanced event listener for promo discount changes
        document.addEventListener('change', function(e) {
            const target = e.target;

            // Check if this is a promo field
            if (target.name === 'promo_id' ||
                target.dataset.fieldName === 'promo_id' ||
                target.getAttribute('wire:model')?.includes('promo_id')) {

                console.log('Promo changed, will recalculate after promo processing...');

                // Wait longer for promo processing to complete
                setTimeout(() => {
                    recalculateTotal();
                    triggerFilamentUpdate();
                }, 500); // Increased delay for promo processing
            }

            // Check if this is a promo discount field
            if (target.name === 'promo_discount' ||
                target.dataset.fieldName === 'promo_discount' ||
                target.getAttribute('wire:model')?.includes('promo_discount')) {

                console.log('Promo discount changed, recalculating...');
                setTimeout(() => {
                    recalculateTotal();
                }, 100);
            }

            // Check if this is is_free toggle
            if (target.name === 'is_free' ||
                target.dataset.fieldName === 'is_free' ||
                target.getAttribute('wire:model')?.includes('is_free')) {

                console.log('is_free changed, recalculating...');
                setTimeout(() => {
                    recalculateTotal();
                }, 100);
            }
        });

        // Also listen for input events (for real-time updates)
        document.addEventListener('input', function(e) {
            const target = e.target;

            // Check if any calculation-relevant field changed
            if (target.name === 'promo_discount' ||
                target.dataset.fieldName === 'promo_discount' ||
                target.getAttribute('wire:model')?.includes('promo_discount')) {

                console.log('Promo discount input changed, recalculating...');
                setTimeout(() => {
                    recalculateTotal();
                }, 150);
            }
        });

        // Listen for any field changes that might affect calculation
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' &&
                    (mutation.attributeName === 'value')) {
                    const target = mutation.target;
                    if (target.name === 'service_price' ||
                        target.dataset.fieldName === 'service_price' ||
                        target.name === 'promo_discount' ||
                        target.dataset.fieldName === 'promo_discount') {

                        console.log('Field mutated, recalculating...', target.name || target
                            .dataset.fieldName);
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
                attributeFilter: ['value']
            });
        }

        // Listen for custom Filament events
        document.addEventListener('service-selected', function() {
            console.log('Service selected event triggered');
        });
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
