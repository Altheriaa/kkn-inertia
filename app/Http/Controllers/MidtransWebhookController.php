<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PendaftaranKkn;
use Illuminate\Support\Facades\Storage;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        // Payload mentah
        $payload = $request->getContent();
        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Payload Invalid', ['payload' => $payload]);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Ambil data
        $order_id = $data['order_id'] ?? null;
        $status_code = $data['status_code'] ?? null;
        $gross_amount = $data['gross_amount'] ?? null;
        $transaction_status = $data['transaction_status'] ?? null;
        $fraud_status = $data['fraud_status'] ?? 'accept';
        $signature_key = $data['signature_key'] ?? null;

        // Validasi signature Key ?? cocok apa ga
        $expectedSignature = hash('sha512', $order_id . $status_code . $gross_amount . Config::$serverKey);

        if ($signature_key !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        Log::info("Signature Key Valid");

        $payment = Payment::where('order_id', $order_id)->first();

        if (!$payment) {
            // Log::error("Order tidak ditemukan", ['order_id' => $order_id]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $pendaftaran = PendaftaranKkn::where('payment_id', $payment->id)->first();

        Log::info("Payment ditemukan", [
            'payment_id' => $payment->id,
            'status' => $payment->status
        ]);

        // kalo dah oke, jangan update apapun lagi
        if (in_array($payment->status, ['success', 'failed'])) {
            $namaMahasiswa = $payment->mahasiswa->nama;
            $namaKkn = $payment->jenis_kkn;
            $message = "Halo $namaMahasiswa! Pembayaran $namaKkn Anda dengan Order ID $order_id telah berhasil dibatalkan.";
            // notif wa batal
            $this->sendWhatsAppNotification($payment->mahasiswa->no_hp, $message);

            Log::info("Status final — tidak diupdate");
            return response()->json(['message' => 'Already processed'], 200);
        }

        if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
            if ($fraud_status == 'accept') {

                // Update Payment
                $payment->status = 'success';
                $payment->save();

                // Update Pendaftaran
                if ($pendaftaran) {
                    $pendaftaran->status_pendaftaran = 'valid';
                    $pendaftaran->save();
                }

                // Update Mahasiswa
                if ($payment->mahasiswa) {
                    $payment->mahasiswa->status_kkn = 'Sudah Daftar';
                    $payment->mahasiswa->save();

                    $pdfUrl = $this->cetakTransaksi($order_id);

                    $namaMahasiswa = $payment->mahasiswa->nama;
                    $namaKkn = $payment->jenis_kkn;
                    $message = "Halo $namaMahasiswa! Pembayaran $namaKkn Anda dengan Order ID $order_id telah berhasil.";
                    if ($pdfUrl) {
                        $message .= "\n\nBerikut invoice pembayaran Anda:\n$pdfUrl";
                    }

                    $this->sendWhatsAppNotification($payment->mahasiswa->no_hp, $message);
                }

                Log::info("PEMBAYARAN BERHASIL", ['order_id' => $order_id]);
            }
        } elseif (in_array($transaction_status, ['cancel', 'deny', 'expire'])) {

            // Pembayaran Gagal 
            $payment->status = 'failed';
            $payment->save();

            // Pendaftaran Gagal
            if ($pendaftaran) {
                $pendaftaran->status_pendaftaran = 'failed';
                $pendaftaran->save();
            }

            Log::info("PEMBAYARAN GAGAL/EXPIRE", ['order_id' => $order_id]);

        } elseif ($transaction_status == 'pending') {

            // PAYMENT MENUNGGU
            $payment->status = 'pending';
            $payment->save();

            if ($pendaftaran) {
                $pendaftaran->status_pendaftaran = 'pending';
                $pendaftaran->save();
            }

            if ($payment->mahasiswa) {
                $namaMahasiswa = $payment->mahasiswa->nama;
                $namaKkn = $payment->jenis_kkn;
                $message = "Halo $namaMahasiswa! Pembayaran $namaKkn Anda dengan Order ID $order_id masih dalam 
                status pending. \n \nMohon segera selesaikan pembayaran KKN Anda!";

                $this->sendWhatsAppNotification($payment->mahasiswa->no_hp, $message);
            }

            Log::info("PEMBAYARAN PENDING", ['order_id' => $order_id]);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // Fonte Controller 
    protected function sendWhatsAppNotification($target, $message)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => ltrim($target, '0'),
                'message' => $message,
                'schedule' => 0,
                'typing' => false,
                'delay' => '2',
                'countryCode' => '62',
                'followup' => 0,
                'inboxid' => 0,
                'duration' => 1,
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . env('FONNTE_TOKEN')
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            Log::error('Fonnte cURL error: ' . curl_error($curl));
        }
        curl_close($curl);

        Log::info('Fonnte response', ['response' => $response]);

        return $response;
    }

    public function cetakTransaksi($orderId)
    {
        $payment = Payment::with('mahasiswa')
            ->where('order_id', $orderId)
            ->where('status', 'success') 
            ->first();

        if (!$payment) {
            Log::warning('cetakTransaksi: Payment not found', ['order_id' => $orderId]);
            return null;
        }
        return url('/invoice/' . $orderId);
    }

    // Download Untuk Route URL Publik
    public function downloadInvoice($orderId)
    {
        $payment = Payment::with('mahasiswa')
            ->where('order_id', $orderId)
            ->where('status', 'success')
            ->first();

        if (!$payment) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $data = ['payment' => $payment];
        $pdf = Pdf::loadView('pdf.Mahasiswa.Invoice', $data);

        return $pdf->stream('Invoice-' . $payment->order_id . '.pdf');
    }

}
