@extends('layouts.app')

@section('title', 'Tracking Pencucian Kendaraan')

@section('content')
    <!-- Header -->
    <div class="text-center mb-8 animate-fade-in px-4 pt-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Tracking Pencucian Kendaraan</h1>
        <p class="text-gray-600 text-sm">Lacak progress pencucian kendaraan Anda secara real-time</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 mx-4 animate-slide-up">
        <form id="trackingForm" class="flex flex-col sm:flex-row gap-4">
            @csrf
            <div class="flex-1">
                <label for="invoice" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Invoice
                </label>
                <input type="text" id="invoice" name="invoice" placeholder="Masukkan nomor invoice (contoh: TRX-012345678)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    required>
            </div>
            <div class="flex items-end">
                <button type="submit" id="trackBtn"
                    class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:transform-none">
                    <span id="trackBtnText">🔍 Lacak Sekarang</span>
                    <span id="trackBtnLoading" class="hidden">⏳ Mencari...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden bg-white rounded-2xl shadow-xl p-6 text-center animate-slide-up mx-4">
        <div class="text-blue-500 text-6xl mb-4 animate-spin">⚙️</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Mencari Data...</h3>
        <p class="text-gray-600">Mohon tunggu sebentar</p>
    </div>

    <!-- Tracking Result -->
    <div id="trackingResult" class="hidden animate-slide-up mx-4">
        @include('partials.result')
    </div>

    <!-- Error Message -->
    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 rounded-2xl p-6 text-center animate-slide-up mx-4">
        <div class="text-red-500 text-6xl mb-4">❌</div>
        <h3 class="text-xl font-bold text-red-800 mb-2">Invoice Tidak Ditemukan</h3>
        <p class="text-red-600" id="errorText">Mohon periksa kembali nomor invoice Anda dan coba lagi.</p>
    </div>
@endsection

@push('addon-script')
    <script src="{{ asset('js/tracking.js') }}"></script>
@endpush
