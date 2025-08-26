@extends('layouts.app')

@section('title', 'Tracking ' . $data['invoice'])

@section('content')
    <!-- Header -->
    <div class="text-center mb-8 animate-fade-in px-4 pt-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Tracking Pencucian Kendaraan</h1>
        <p class="text-gray-600 text-sm">Detail tracking untuk invoice: {{ $data['invoice'] }}</p>
    </div>

    <!-- Back to Search Button -->
    <div class="mx-4 mb-6">
        <a href="{{ route('tracking.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Cari Invoice Lain
        </a>
    </div>

    <!-- Tracking Result -->
    <div class="animate-slide-up mx-4">
        <!-- Free Transaction Alert -->
        @if ($data['isFree'])
            <div
                class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 mb-6 text-white animate-bounce-gentle">
                <div class="flex items-center justify-center mb-2">
                    <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4">
                        <span class="text-3xl">🎁</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-1">Selamat! Transaksi Anda GRATIS!</h3>
                        <p class="text-green-100 text-sm">Anda mendapat layanan pencucian gratis dari kami</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3 mt-4 text-black">
                    <p class="text-center text-sm font-medium">
                        ✨ Terima kasih telah menjadi pelanggan setia kami ✨
                    </p>
                </div>
            </div>
        @endif

        <!-- Order Info -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $data['invoice'] }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <span>📅 <strong>Tanggal:</strong> {{ $data['date'] }}</span>
                        <span>🚗 <strong>Kendaraan:</strong> {{ $data['vehicle'] }}</span>
                        <span>🧽 <strong>Layanan:</strong> {{ $data['service'] }}</span>
                        <span>💰 <strong>Status Pembayaran:</strong> {{ $data['isPaid'] }}</span>
                    </div>
                </div>
                <div class="text-right">
                    @if ($data['isFree'])
                        <div class="text-2xl font-bold text-green-600">GRATIS</div>
                        <div class="text-sm text-gray-500">Transaksi Gratis</div>
                        <div class="text-xs text-gray-400 line-through">{{ $data['totalPrice'] }}</div>
                    @else
                        <div class="text-2xl font-bold text-blue-600">{{ $data['totalPrice'] }}</div>
                        <div class="text-sm text-gray-500">Total Pembayaran</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">👤 Informasi Pelanggan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Nama:</span>
                    <span class="font-medium ml-2">{{ $data['customer']['name'] }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Telepon:</span>
                    <span class="font-medium ml-2">{{ $data['customer']['phone'] }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Tracking -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">📍 Status Pencucian</h3>

            <div class="relative">
                @php
                    $completedSteps = 0;
                    $isCompleted = $transaction->status === 'selesai' && $transaction->is_paid;

                    foreach ($data['steps'] as $step) {
                        if ($step['status'] === 'completed') {
                            $completedSteps++;
                        } elseif ($step['status'] === 'current') {
                            $completedSteps += 0.5;
                        }
                    }

                    if ($isCompleted) {
                        $progressPercentage = 80;
                    } else {
                        $progressPercentage = ($completedSteps / count($data['steps'])) * 100;
                    }
                @endphp

                <div class="absolute left-6 top-8 bottom-8 w-0.5 bg-gray-200"></div>
                <div class="absolute left-6 top-8 w-0.5 bg-gradient-to-b from-green-500 to-blue-500 transition-all duration-1000 ease-out"
                    style="height: {{ $progressPercentage }}%">
                </div>

                <div class="space-y-6">
                    @foreach ($data['steps'] as $index => $step)
                        @php
                            $isLastStep = $index === count($data['steps']) - 1;

                            switch ($step['status']) {
                                case 'completed':
                                    $statusClasses = 'bg-green-500 border-green-500 text-white';
                                    $iconClasses = 'text-green-600';
                                    $contentClasses = 'text-gray-900';
                                    break;
                                case 'current':
                                    $statusClasses = 'bg-blue-500 border-blue-500 text-white animate-pulse';
                                    $iconClasses = 'text-blue-600';
                                    $contentClasses = 'text-gray-900 font-medium';
                                    break;
                                default:
                                    $statusClasses = 'bg-gray-200 border-gray-300 text-gray-400';
                                    $iconClasses = 'text-gray-400';
                                    $contentClasses = 'text-gray-500';
                            }
                        @endphp

                        <div class="relative flex items-start animate-fade-in"
                            style="animation-delay: {{ $index * 0.1 }}s">
                            <div
                                class="relative z-10 flex items-center justify-center w-12 h-12 rounded-full border-4 {{ $statusClasses }} shadow-lg">
                                <span class="text-lg">{{ $step['icon'] }}</span>
                            </div>
                            <div class="ml-4 flex-1 {{ $isLastStep ? 'pb-0' : 'pb-6' }}">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <h4 class="text-lg font-semibold {{ $contentClasses }}">{{ $step['title'] }}</h4>
                                    @if ($step['time'])
                                        <div class="text-sm {{ $iconClasses }} mt-1 sm:mt-0">
                                            🕐 {{ $step['time'] }} • {{ $step['date'] }}
                                        </div>
                                    @endif
                                </div>
                                <p class="text-sm {{ $contentClasses }} mt-1">{{ $step['description'] }}</p>
                                @if ($step['status'] === 'current')
                                    <div
                                        class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ⚡ Sedang Berlangsung
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-style')
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce-gentle {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
            opacity: 0;
        }

        .animate-slide-up {
            animation: slide-up 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-bounce-gentle {
            animation: bounce-gentle 2s infinite;
        }
    </style>
@endpush
