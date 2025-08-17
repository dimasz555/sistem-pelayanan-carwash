<div class="p-2 sm:p-4 lg:p-6">
    {{-- Transactions List --}}
    @if ($transactions->count() > 0)
        <div class="space-y-3 sm:space-y-4">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
                Riwayat Transaksi ({{ $transactions->count() }} transaksi)
            </h3>

            {{-- Mobile Card View (visible on small screens) --}}
            <div class="block lg:hidden space-y-3">
                @foreach ($transactions as $transaction)
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                        {{-- Header --}}
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">
                                        {{ $transaction->invoice }}
                                    </h4>
                                    {{-- @if ($transaction->is_free)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            Gratis
                                        </span>
                                    @endif --}}
                                </div>
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $transaction->transaction_at
                                        ? \Carbon\Carbon::parse($transaction->transaction_at)->locale('id')->timezone('Asia/Jakarta')->isoFormat('dddd, DD/MM/YYYY, HH:mm')
                                        : '-' }}
                                </p>
                            </div>

                            {{-- Price --}}
                            <div class="text-right">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">
                                    @if ($transaction->is_free)
                                        <span class="text-green-600 dark:text-green-400">Gratis</span>
                                    @else
                                        <span class="text-xs sm:text-sm">Rp
                                            {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Content Grid --}}
                        <div class="grid grid-cols-2 gap-3 text-xs sm:text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Layanan</p>
                                <p class="text-gray-900 dark:text-gray-100 mt-1">
                                    {{ $transaction->service?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Kendaraan</p>
                                <div class="mt-1">
                                    <p class="text-gray-900 dark:text-gray-100 font-medium">
                                        {{ $transaction->vehicle_name ?: '-' }}</p>
                                    <p class="text-gray-400 dark:text-gray-500 text-xs">
                                        {{ $transaction->plate_number ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Status Badges --}}
                        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            @php
                                $statusColors = [
                                    'menunggu' =>
                                        'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', // warning
                                    'proses' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', // primary
                                    'selesai' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', // success
                                    'batal' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', // danger
                                ];
                                $statusLabels = [
                                    'menunggu' => 'Menunggu',
                                    'proses' => 'Proses',
                                    'selesai' => 'Selesai',
                                    'batal' => 'Batal',
                                ];
                            @endphp

                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$transaction->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                {{ $statusLabels[$transaction->status] ?? ucfirst($transaction->status) }}
                            </span>

                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->is_paid ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                {{ $transaction->is_paid ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop Table View (visible on large screens) --}}
            <div class="hidden lg:block">
                <div
                    class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Invoice
                                    </th>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Layanan
                                    </th>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kendaraan
                                    </th>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Pembayaran
                                    </th>
                                    <th
                                        class="px-3 xl:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                                        <td
                                            class="px-3 xl:px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <div class="flex flex-col xl:flex-row xl:items-center xl:gap-2">
                                                <span class="font-medium">{{ $transaction->invoice }}</span>
                                                {{-- @if ($transaction->is_free)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 mt-1 xl:mt-0">
                                                        Gratis
                                                    </span>
                                                @endif --}}
                                            </div>
                                        </td>
                                        <td class="px-3 xl:px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="max-w-[120px] xl:max-w-none">
                                                {{ $transaction->transaction_at
                                                    ? \Carbon\Carbon::parse($transaction->transaction_at)->locale('id')->timezone('Asia/Jakarta')->isoFormat('DD/MM/YYYY, HH:mm')
                                                    : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-3 xl:px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="max-w-[100px] xl:max-w-none truncate"
                                                title="{{ $transaction->service?->name ?? 'N/A' }}">
                                                {{ $transaction->service?->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-3 xl:px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="max-w-[120px] xl:max-w-none">
                                                <p class="font-medium text-gray-900 dark:text-gray-100 truncate"
                                                    title="{{ $transaction->vehicle_name ?: '-' }}">
                                                    {{ $transaction->vehicle_name ?: '-' }}
                                                </p>
                                                <p class="text-gray-400 dark:text-gray-500 text-xs truncate"
                                                    title="{{ $transaction->plate_number ?: '-' }}">
                                                    {{ $transaction->plate_number ?: '-' }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-3 xl:px-6 py-4">
                                            @php
                                                $statusColors = [
                                                    'menunggu' =>
                                                        'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', // warning
                                                    'proses' =>
                                                        'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', // primary
                                                    'selesai' =>
                                                        'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', // success
                                                    'batal' =>
                                                        'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', // danger
                                                ];
                                                $statusLabels = [
                                                    'menunggu' => 'Menunggu',
                                                    'proses' => 'Proses',
                                                    'selesai' => 'Selesai',
                                                    'batal' => 'Batal',
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$transaction->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                                {{ $statusLabels[$transaction->status] ?? ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 xl:px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->is_paid ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                                {{ $transaction->is_paid ? 'Sudah Dibayar' : 'Belum Dibayar' }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-3 xl:px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            @if ($transaction->is_free)
                                                <span class="text-green-600 dark:text-green-400">Gratis</span>
                                            @else
                                                <div class="max-w-[100px] xl:max-w-none">
                                                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tablet View (visible on medium screens) --}}
            <div class="hidden md:block lg:hidden">
                <div
                    class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Invoice
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Kendaraan
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                                        <td class="px-4 py-4 text-sm">
                                            <div class="space-y-1">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $transaction->invoice }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $transaction->service?->name ?? 'N/A' }}</div>
                                                {{-- @if ($transaction->is_free)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                        Gratis
                                                    </span>
                                                @endif --}}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaction->transaction_at
                                                ? \Carbon\Carbon::parse($transaction->transaction_at)->locale('id')->timezone('Asia/Jakarta')->isoFormat('DD/MM/YY, HH:mm')
                                                : '-' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <div class="space-y-1">
                                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $transaction->vehicle_name ?: '-' }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                                    {{ $transaction->plate_number ?: '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="space-y-1">
                                                @php
                                                    $statusColors = [
                                                        'menunggu' =>
                                                            'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200', // warning
                                                        'proses' =>
                                                            'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200', // primary
                                                        'selesai' =>
                                                            'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200', // success
                                                        'batal' =>
                                                            'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200', // danger
                                                    ];
                                                    $statusLabels = [
                                                        'menunggu' => 'Menunggu',
                                                        'proses' => 'Proses',
                                                        'selesai' => 'Selesai',
                                                        'batal' => 'Batal',
                                                    ];
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$transaction->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                                    {{ $statusLabels[$transaction->status] ?? ucfirst($transaction->status) }}
                                                </span>
                                                <br>
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->is_paid ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                                    {{ $transaction->is_paid ? 'Lunas' : 'Belum Lunas' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            @if ($transaction->is_free)
                                                <span class="text-green-600 dark:text-green-400">Gratis</span>
                                            @else
                                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-8 sm:py-12">
            <div class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Belum ada transaksi</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pelanggan ini belum melakukan transaksi apapun.</p>
        </div>
    @endif
</div>
