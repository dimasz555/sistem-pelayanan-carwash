@extends('layouts.app')

@section('title', 'Tracking Pencucian Kendaraan')

@section('content')
    <!-- Header -->
    <div class="text-center mb-8 animate-fade-in px-4 pt-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Tracking Pencucian Kendaraan</h1>
        <p class="text-gray-600 text-sm">Lacak progress pencucian kendaraan Anda secara real-time</p>
    </div>

    <!-- Error Message from Session -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center animate-slide-up mx-4 mb-6">
            <div class="text-red-500 text-6xl mb-4">❌</div>
            <h3 class="text-xl font-bold text-red-800 mb-2">Invoice Tidak Ditemukan</h3>
            <p class="text-red-600">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Search Form -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 mx-4 animate-slide-up">
        <form id="trackingForm" class="flex flex-col sm:flex-row gap-4" action="{{ route('tracking.search') }}" method="POST">
            @csrf
            <div class="flex-1">
                <label for="invoice" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Invoice
                </label>
                <input type="text" id="invoice" name="invoice" 
                       placeholder="Masukkan nomor invoice (contoh: TRX-012345678)"
                       value="{{ old('invoice') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('invoice') border-red-500 @enderror"
                       required>
                @error('invoice')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
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

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center animate-slide-up mx-4 mt-6">
        <div class="text-blue-500 text-4xl mb-4">💡</div>
        <h3 class="text-lg font-bold text-blue-800 mb-2">Tips Pencarian</h3>
        <p class="text-blue-600 text-sm">
            Masukkan nomor invoice yang Anda terima saat melakukan transaksi. 
            Format biasanya seperti: INV-123456789 atau TRX-012345678
        </p>
    </div>
@endsection

@push('addon-script')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("trackingForm");
            const trackBtn = document.getElementById("trackBtn");
            const trackBtnText = document.getElementById("trackBtnText");
            const trackBtnLoading = document.getElementById("trackBtnLoading");
            const loadingState = document.getElementById("loadingState");

            form.addEventListener("submit", function (e) {
                // Show loading state
                trackBtn.disabled = true;
                trackBtnText.classList.add("hidden");
                trackBtnLoading.classList.remove("hidden");
                loadingState.classList.remove("hidden");
            });

            // Allow enter key to trigger search
            document.getElementById("invoice").addEventListener("keypress", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    form.submit();
                }
            });
        });
    </script>
@endpush

@push('addon-style')
<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .animate-slide-up {
        animation: slide-up 0.8s ease-out forwards;
        opacity: 0;
    }
</style>
@endpush