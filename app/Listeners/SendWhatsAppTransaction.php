<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\WhatsappApiToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWhatsAppTransaction
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\TransactionCreated  $event
     * @return void
     */
    public function handle(TransactionCreated $event)
    {
        try {
            // Ambil data transaksi
            $transaction = $event->transaction;
            $customer = $transaction->customer; // Relasi ke customer
            $nama = $customer->name ?? 'Customer';
            $sapaan = $customer->sapaan ?? '';
            $telepon = $customer->phone;
            $invoice = $transaction->invoice;
            $biaya = number_format($transaction->total_price ?? $transaction->service_price ?? 0, 0, ',', '.');
            $service = $transaction->service->name ?? 'Layanan Cuci';
            $vehicle = $transaction->vehicle_name ?? '';
            $plate = $transaction->plate_number ?? '';
            $queue = $transaction->queue_number ?? '';
            $is_free = $transaction->is_free;

            Carbon::setLocale('id');
            $tanggal = Carbon::parse($transaction->transaction_at)
                ->setTimezone('Asia/Jakarta')
                ->translatedFormat('l, d F Y H:i');

            // Ambil token WhatsApp aktif
            $token = WhatsappApiToken::where('status', 'active')->first();

            // Jika token tidak ditemukan, log error
            if (!$token) {
                Log::error('Token WhatsApp tidak ditemukan untuk transaction: ' . $transaction->id);
                return;
            }

            // Pesan untuk vehicle wash (disesuaikan dengan struktur database)
            $fullName = trim($sapaan . ' ' . $nama);
            $priceInfo = $is_free ? "*GRATIS* 🎉" : "*Rp {$biaya}*";

            $trackingUrl = route('tracking.show', ['invoice' => $invoice]);

            $message = "*PENCUCIAN KENDARAAN*\n\n" .
                "Hai *{$fullName}!*\n\n" .
                "Transaksi cuci kendaraan Anda telah berhasil dibuat.\n\n" .
                "📋 *Detail Transaksi:*\n" .
                "• Invoice: *{$invoice}*\n" .
                "• Antrian: *#{$queue}*\n" .
                "• Layanan: *{$service}*\n" .
                "• Kendaraan: *{$vehicle}*\n" .
                "• Plat Nomor: *{$plate}*\n" .
                "• Total Biaya: {$priceInfo}\n" .
                "• Waktu: *{$tanggal} WIB*\n\n";

            if ($is_free) {
                $message .= "🎊 *Selamat! Ini adalah cuci gratis untuk Anda!*\n\n";
            }

            $message .= "Cek status pencucian kendaraan Anda secara berkala di sini.\n" .
                "{$trackingUrl}\n" .
                "Terima kasih telah menggunakan layanan kami! 🙏\n\n" .
                "_Pesan ini dikirim otomatis_";

            // Data yang akan dikirimkan ke API WhatsApp
            $data = [
                'api_key' => $token->api_token,
                'sender'  => $token->sender,
                'number'  => $this->formatPhoneNumber($telepon),
                'message' => $message,
            ];

            // Kirim pesan WhatsApp menggunakan cURL
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $token->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10, // Tambahkan timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            // Enhanced logging
            if ($error) {
                Log::error('CURL Error saat kirim WhatsApp', [
                    'transaction_id' => $transaction->id,
                    'error' => $error
                ]);
            } elseif ($httpCode >= 200 && $httpCode < 300) {
                Log::info('WhatsApp berhasil dikirim', [
                    'transaction_id' => $transaction->id,
                    'phone' => $telepon,
                    'response' => $response
                ]);
            } else {
                Log::error('WhatsApp API Error', [
                    'transaction_id' => $transaction->id,
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception saat kirim WhatsApp', [
                'transaction_id' => $event->transaction->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Format nomor telepon ke format internasional
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
