<!-- Order Info -->
<div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2"><span id="invoiceNumber"></span></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <span>📅 <strong>Tanggal:</strong> <span id="orderDate"></span></span>
                <span>🚗 <strong>Kendaraan:</strong> <span id="vehicleInfo"></span></span>
                <span>🧽 <strong>Layanan:</strong> <span id="serviceType"></span></span>
                <span>💰 <strong>Status Pembayaran:</strong> <span id="isPaid"></span></span>
            </div>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-blue-600" id="totalPrice"></div>
            <div class="text-sm text-gray-500">Total Pembayaran</div>
        </div>
    </div>
</div>

<!-- Customer Info -->
<div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">👤 Informasi Pelanggan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-gray-600">Nama:</span>
            <span class="font-medium ml-2" id="customerName"></span>
        </div>
        <div>
            <span class="text-gray-600">Telepon:</span>
            <span class="font-medium ml-2" id="customerPhone"></span>
        </div>
    </div>
</div>

<!-- Progress Tracking -->
<div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
    <h3 class="text-xl font-bold text-gray-800 mb-6">📍 Status Pencucian</h3>

    <div class="relative" id="progressContainer">
        <div id="progressBackground"
            class="absolute left-6 top-8 bottom-8 w-0.5 bg-gray-200 transition-all duration-500 ease-out">
        </div>
        <div id="progressLine"
            class="absolute left-6 top-8 w-0.5 bg-gradient-to-b from-green-500 to-blue-500 transition-all duration-1000 ease-out"
            style="height: 0%">
        </div>

        <div id="trackingSteps" class="space-y-6">
            <!-- Steps will be dynamically inserted here -->
        </div>
    </div>
</div>
