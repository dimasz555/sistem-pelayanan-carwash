<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

class TrackingController extends Controller
{
    public function index()
    {
        return view('pages.tracking.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string'
        ], [
            'invoice.required' => 'Nomor invoice harus diisi'
        ]);

        $invoice = strtoupper(trim($request->invoice));

        // Redirect to the new route with invoice parameter
        return redirect()->route('tracking.show', ['invoice' => $invoice]);
    }

    public function show($invoice)
    {
        $invoice = strtoupper(trim($invoice));

        $transaction = Transaction::with(['customer', 'service'])
            ->where('invoice', $invoice)
            ->first();

        if (!$transaction) {
            // If accessed via AJAX (for API-like response)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }
            
            // If accessed directly via URL, redirect back with error
            return redirect()->route('tracking.index')
                ->with('error', 'Invoice tidak ditemukan');
        }

        // Generate tracking steps based on transaction status and timestamps
        $steps = $this->generateTrackingSteps($transaction);

        $data = [
            'invoice' => $transaction->invoice,
            'date' => $transaction->transaction_at ? Carbon::parse($transaction->transaction_at)->locale('id')->setTimezone('Asia/Jakarta')->translatedFormat('d F Y'): '-',
            'vehicle' => $transaction->vehicle_name . ' - ' . $transaction->plate_number,
            'service' => $transaction->service->name ?? 'Service tidak tersedia',
            'totalPrice' => 'Rp ' . number_format($transaction->total_price, 0, ',', '.'),
            'isPaid' => $transaction->is_paid ? 'Sudah Dibayar' : 'Belum Dibayar',
            'customer' => [
                'name' => $transaction->customer->name ?? 'Customer tidak tersedia',
                'phone' => $transaction->customer->phone ?? '-'
            ],
            'steps' => $steps
        ];

        // If accessed via AJAX
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data + [
                    'is_completed' => $transaction->status === 'selesai' && $transaction->is_paid
                ]
            ]);
        }

        // If accessed directly via URL, return view with data
        return view('pages.tracking.result', compact('data', 'transaction'));
    }

    private function generateTrackingSteps($transaction)
    {
        $steps = [
            [
                'title' => 'Pesanan Diterima',
                'description' => 'Pesanan Anda telah kami terima dan sedang diproses',
                'icon' => '📋',
                'status' => 'completed', // Selalu completed karena transaksi sudah ada
                'time' => $transaction->transaction_at ? Carbon::parse($transaction->transaction_at)->locale('id')->setTimezone('Asia/Jakarta')->format('H:i') : '',
                'date' => $transaction->transaction_at ? Carbon::parse($transaction->transaction_at)->locale('id')->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') : ''
            ],
            [
                'title' => 'Kendaraan Masuk Antrian',
                'description' => 'Kendaraan Anda sudah masuk dalam antrian pencucian',
                'icon' => '⏳',
                'status' => $this->getStepStatus('menunggu', $transaction->status),
                'time' => $transaction->waiting_at ? Carbon::parse($transaction->waiting_at)->locale('id')->setTimezone('Asia/Jakarta')->format('H:i') : '',
                'date' => $transaction->waiting_at ? Carbon::parse($transaction->waiting_at)->locale('id')->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') : ''
            ],
            [
                'title' => 'Proses Pencucian Dimulai',
                'description' => 'Tim kami sedang mencuci kendaraan Anda dengan teliti',
                'icon' => '🧽',
                'status' => $this->getStepStatus('proses', $transaction->status),
                'time' => $transaction->processing_at ? Carbon::parse($transaction->processing_at)->locale('id')->setTimezone('Asia/Jakarta')->format('H:i') : '',
                'date' => $transaction->processing_at ? Carbon::parse($transaction->processing_at)->locale('id')->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') : ''
            ],
            [
                'title' => 'Selesai - Siap Diambil',
                'description' => 'Kendaraan Anda sudah selesai dan siap untuk diambil',
                'icon' => '🎉',
                'status' => $this->getFinalStepStatus($transaction),
                'time' => $transaction->done_at ? Carbon::parse($transaction->done_at)->locale('id')->setTimezone('Asia/Jakarta')->format('H:i') : '',
                'date' => $transaction->done_at ? Carbon::parse($transaction->done_at)->locale('id')->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') : ''
            ]
        ];

        return $steps;
    }

    private function getStepStatus($stepStatus, $currentStatus)
    {
        // Mapping status dari database ke urutan tahapan
        $statusOrder = [
            'menunggu' => 1,   // Kendaraan masuk antrian
            'proses' => 2,     // Proses pencucian dimulai
            'selesai' => 3     // Selesai - siap diambil
        ];

        // Mapping step ke urutan
        $stepOrder = [
            'menunggu' => 1,
            'proses' => 2,
            'selesai' => 3
        ];

        $currentIndex = $statusOrder[$currentStatus] ?? 0;
        $stepIndex = $stepOrder[$stepStatus] ?? 0;

        if ($stepIndex < $currentIndex) {
            return 'completed';
        } elseif ($stepIndex === $currentIndex) {
            return 'current';
        } else {
            return 'pending';
        }
    }

    private function getFinalStepStatus($transaction)
    {
        // Jika status selesai dan sudah dibayar, tandai sebagai completed
        if ($transaction->status === 'selesai' && $transaction->is_paid) {
            return 'completed';
        }

        // Jika status selesai tapi belum dibayar, tetap dianggap current
        if ($transaction->status === 'selesai' && !$transaction->is_paid) {
            return 'current';
        }

        // Jika belum sampai status selesai
        return $this->getStepStatus('selesai', $transaction->status);
    }
}