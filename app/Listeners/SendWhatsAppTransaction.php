<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Events\TransactionPaid;
use App\Events\VehicleDone;
use App\Models\WhatsappApiToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWhatsAppTransaction
{
    /**
     * Handle TransactionCreated event
     */
    public function handleCreated(TransactionCreated $event)
    {
        $this->sendWhatsAppMessage($event->transaction, 'created');
    }

    /**
     * Handle TransactionPaid event
     */
    public function handlePaid(TransactionPaid $event)
    {
        $this->sendWhatsAppMessage($event->transaction, 'paid');
    }

    /**
     * Handle VehicleDone event
     */
    public function handleVehicleDone(VehicleDone $event)
    {
        $this->sendWhatsAppMessage($event->transaction, 'done');
    }



    /**
     * Universal method untuk kirim WhatsApp
     */
    private function sendWhatsAppMessage($transaction, $type)
    {
        try {
            // Ambil data transaksi
            $customer = $transaction->customer;
            $nama = $customer->name ?? 'Customer';
            $sapaan = $customer->sapaan ?? '';
            $telepon = $customer->phone;

            // Ambil token WhatsApp aktif
            $token = WhatsappApiToken::where('status', 'active')->first();

            if (!$token) {
                Log::error("Token WhatsApp tidak ditemukan untuk {$type}", [
                    'transaction_id' => $transaction->id
                ]);
                return;
            }

            // Generate pesan berdasarkan type
            $message = $this->buildMessage($transaction, $type);

            // Data untuk API
            $data = [
                'api_key' => $token->api_token,
                'sender'  => $token->sender,
                'number'  => $this->formatPhoneNumber($telepon),
                'message' => $message,
            ];

            // Kirim via cURL
            $response = $this->sendCurl($token->url, $data);

            // Log hasil
            $this->logResponse($response, $transaction, $type);
        } catch (\Exception $e) {
            Log::error("Exception saat kirim WhatsApp {$type}", [
                'transaction_id' => $transaction->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Build message berdasarkan type event
     */
    private function buildMessage($transaction, $type)
    {
        $customer = $transaction->customer;
        $nama = $customer->name ?? 'Customer';
        $sapaan = $customer->sapaan ?? '';
        $fullName = trim($sapaan . ' ' . $nama);

        $invoice = $transaction->invoice;
        $biaya = number_format($transaction->total_price ?? $transaction->service_price ?? 0, 0, ',', '.');
        $service = $transaction->service->name ?? 'Layanan Cuci';
        $vehicle = $transaction->vehicle_name ?? '';
        $plate = $transaction->plate_number ?? '';
        $queue = $transaction->queue_number ?? '';
        $is_free = $transaction->is_free;

        Carbon::setLocale('id');

        switch ($type) {
            case 'created':
                $tanggal = Carbon::parse($transaction->transaction_at)
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat('l, d F Y H:i');

                $trackingUrl = route('tracking.show', ['invoice' => $invoice]);

                $priceInfo = $is_free ? "*GRATIS* 🎉" : "*Rp{$biaya}*";

                $message = "🚗 *PENCUCIAN KENDARAAN*\n\n" .
                    "Halo *{$fullName}!*\n\n" .
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
                break;

            case 'paid':
                $paidTime = Carbon::parse($transaction->paid_at ?? now())
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat('l, d F Y H:i');

                $priceInfo = $is_free ? "*GRATIS* 🎉" : "*Rp {$biaya}*";

                // Hitung loyalty program
                $totalWash = $customer->total_wash ?? 0;
                $freeWashCount = $customer->free_wash_count ?? 0;
                $washesUntilFree = 10 - ($totalWash % 10);
                $isEligibleForNextFree = ($totalWash % 10 == 0 && $totalWash > 0);

                $message = "🙏 *Halo {$fullName}!*\n\n";

                if ($is_free) {
                    $message .= "Selamat! Anda telah menggunakan bonus cuci gratis ke-{$freeWashCount}! 🎊\n\n";
                } else {
                    $message .= "Terima kasih atas pembayaran yang telah dilakukan! 💰\n\n";
                }

                $message .= "*✅ PEMBAYARAN DIKONFIRMASI*\n\n" .
                    "Pembayaran untuk cuci kendaraan Anda telah berhasil kami terima dan dikonfirmasi.\n" .
                    // "📋 *Detail Transaksi:*\n" .
                    // "• Invoice: *{$invoice}*\n" .
                    // "• Antrian: *#{$queue}*\n" .
                    // "• Layanan: *{$service}*\n" .
                    // "• Kendaraan: *{$vehicle}*\n" .
                    // "• Plat Nomor: *{$plate}*\n" .
                    // "• Total: {$priceInfo}\n" .
                    "Waktu Pembayaran: *{$paidTime} WIB*\n\n" .
                    "🏆 *PROGRAM LOYALITAS PELANGGAN*\n" .
                    "• Total Cuci Anda: *{$totalWash}x*\n" .
                    "• Bonus Gratis Didapat: *{$freeWashCount}x*\n";

                if ($isEligibleForNextFree) {
                    $message .= "• 🎉 *Selamat! Cuci berikutnya GRATIS!*\n\n" .
                        "Anda telah mencapai 10x pencucian! Kunjungi kami lagi untuk mendapatkan cuci gratis Anda! 🎊\n\n";
                } else {
                    $message .= "• Kurang *{$washesUntilFree}x* lagi untuk bonus cuci GRATIS!\n\n" .
                        "💡 *Tips:* Setiap 10x cuci, dapatkan 1x cuci gratis! Ajak keluarga dan teman-teman untuk cuci bersama ya! 🚗👨‍👩‍👧‍👦\n\n";
                }

                $message .= "Sekali lagi, terima kasih atas kepercayaan Anda kepada layanan kami! 🚗✨\n\n" .
                    "_Pesan ini dikirim otomatis_";
                break;

            case 'done':
                $doneTime = Carbon::parse($transaction->done_at ?? now())
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat('l, d F Y H:i');
                $message = "🚗 *PENCUCIAN KENDARAAN SELESAI!*\n\n" .
                    "Hai *{$fullName}!*\n\n" .
                    "Kami senang menginformasikan bahwa pencucian kendaraan Anda telah selesai.\n\n" .
                    "📋 *Detail Transaksi:*\n" .
                    "• Invoice: *{$invoice}*\n" .
                    "• Antrian: *#{$queue}*\n" .
                    "• Layanan: *{$service}*\n" .
                    "• Kendaraan: *{$vehicle}*\n" .
                    "• Plat Nomor: *{$plate}*\n" .
                    "• Total Biaya: *Rp{$biaya}*\n" .
                    "• Waktu Selesai: *{$doneTime} WIB*\n\n"
                    . "Silakan ambil kendaraan Anda di lokasi kami.\n\n" .
                    "Terima kasih telah menggunakan layanan kami! 🙏\n\n" .
                    "_Pesan ini dikirim otomatis_";
                break;

            default:
                $message = "Update transaksi untuk invoice: {$invoice}";
        }

        return $message;
    }

    /**
     * Kirim via cURL
     */
    private function sendCurl($url, $data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
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

        return [
            'response' => $response,
            'http_code' => $httpCode,
            'error' => $error
        ];
    }

    /**
     * Log response
     */
    private function logResponse($result, $transaction, $type)
    {
        if ($result['error']) {
            Log::error("CURL Error saat kirim WhatsApp {$type}", [
                'transaction_id' => $transaction->id,
                'error' => $result['error']
            ]);
        } elseif ($result['http_code'] >= 200 && $result['http_code'] < 300) {
            Log::info("WhatsApp {$type} berhasil dikirim", [
                'transaction_id' => $transaction->id,
                'phone' => $transaction->customer->phone,
                'response' => $result['response']
            ]);
        } else {
            Log::error("WhatsApp API Error {$type}", [
                'transaction_id' => $transaction->id,
                'http_code' => $result['http_code'],
                'response' => $result['response']
            ]);
        }
    }

    /**
     * Format nomor telepon ke format internasional
     */
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
