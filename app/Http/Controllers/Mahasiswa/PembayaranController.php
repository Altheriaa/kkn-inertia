<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;
use Inertia\Inertia;
use App\Models\JadwalKkn;
use App\Models\Mahasiswa;
use App\Models\Payment;
use App\Models\PendaftaranKkn;
use Midtrans\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index() {

        $hariini = now()->format('Y-m-d');

        $cekJadwal = JadwalKkn::where('is_active', true)
            ->whereDate('tanggal_dibuka', '<=', $hariini)
            ->whereDate('tanggal_ditutup', '>=', $hariini)
            ->first();

        if (! $cekJadwal) {
            return back()->withErrors([
                'error' => 'Pendaftaran KKN belum dibuka.',
            ]);
        }

        // 1. Ambil HANYA ID mahasiswa dari session
        $mahasiswaId = Session::get('mahasiswa_data')['id'];
        // 2. Cari mahasiswa itu di tabel lokal menggunakan ID-nya
        $mahasiswa = Mahasiswa::find($mahasiswaId);

        // cek apa udah isi biodata apa ga? kalo ga direct ke halaman biodata   
        if (
            !$mahasiswa->no_hp ||
            !$mahasiswa->no_hp_wali ||
            !$mahasiswa->jenis_kelamin ||
            !$mahasiswa->ukuran_jacket_rompi ||
            !$mahasiswa->keahlian
        ) {
            return redirect('/mahasiswa/pendaftaran')
                ->with('warning', 'Lengkapi biodata terlebih dahulu sebelum memilih KKN.');
        }

        // Jika bukan mahasiswa direct 
        if (!$mahasiswaId) {
            return redirect()->route('login')->withErrors('Sesi habis.');
        }

        $pendingPayment = Payment::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->first();

        // panggil Api endpoinst jenis kkn 
        $siakadApiUrl = 'https://mini-siakad.cloud/api/kkn/jenis';
        $secretKeySiakad = env('SYSTEM_API_KEY');
        $jenisKknList = [];

        try {
            // UBAH BAGIAN INI: Tambahkan withHeaders()
            $response = Http::withHeaders([
                'X-SYSTEM-KEY' => $secretKeySiakad,
                'Accept' => 'application/json'
            ])->get($siakadApiUrl);
            // AKHIR PERUBAHAN

            if ($response->successful()) {
                $jenisKknList = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
        }

        // 3. Kirim OBJEK mahasiswa ke view
        return Inertia::render('Mahasiswa/Pembayaran', [
            'mahasiswa' => $mahasiswa,
            'jenisKknList' => $jenisKknList,
            'pendingPayment' => $pendingPayment
        ]);
    }

    public function createTransaction(Request $request)
    {
        $request->validate(['jenis_kkn_id' => 'required|integer']);
        $jenisKknIdDipilih = $request->jenis_kkn_id;

        $jadwalKkn = JadwalKkn::where('is_active', true)
            ->whereDate('tanggal_dibuka', '<=', now())
            ->whereDate('tanggal_ditutup', '>=', now())
            ->orderBy('id_siakad', 'desc')
            ->first();

        if (!$jadwalKkn) {
            return response()->json([
                'message' => 'Tidak ada periode KKN yang sedang dibuka.',
                'status' => 'failed'
            ], 400);
        }

        $jadwalKknId = $jadwalKkn->id;

        $mahasiswaData = Session::get('mahasiswa_data');
        $mahasiswaId = $mahasiswaData['id'];

        // Cek apakah mahasiswa sudah pernah daftar KKN valid
        $pendaftaran = PendaftaranKkn::where('mahasiswa_id', $mahasiswaId)->first();
        if ($pendaftaran && $pendaftaran->status_pendaftaran === 'valid') {
            return response()->json([
                'message' => 'Anda sudah terdaftar KKN.',
                'status' => 'failed'
            ], 400);
        }

        // 1. Validasi Syarat ke SIAKAD
        $siakadToken = Session::get('siakad_token');
        $validasiResponse = Http::withToken($siakadToken)
            ->post('https://mini-siakad.cloud/api/kkn/syarat', [
                'jenis_kkn_id' => $jenisKknIdDipilih
            ]);

        if ($validasiResponse->failed()) {
            return response()->json($validasiResponse->json(), $validasiResponse->status());
        }

        $dataFromSiakad = $validasiResponse->json()['data'];
        $biayaKkn = $dataFromSiakad['biaya'];

        return DB::transaction(function () use ($jenisKknIdDipilih, $mahasiswaId, $mahasiswaData, $dataFromSiakad, $biayaKkn, $jadwalKkn, $pendaftaran) {
            
            $orderId = 'KKN-' . $jenisKknIdDipilih . '-' . $mahasiswaId . '-' . time();

            // 2. Buat Payment
            $payment = Payment::create([
                'order_id' => $orderId,
                'mahasiswa_id' => $mahasiswaId,
                'jenis_kkn_id' => $jenisKknIdDipilih,
                'jenis_kkn' => $dataFromSiakad['jenis_kkn'],
                'amount' => $biayaKkn,
                'status' => 'pending',
            ]);

            // 3. Buat Pendaftaran jika belum ada
            if (!$pendaftaran) {
                PendaftaranKkn::create([
                    'mahasiswa_id' => $mahasiswaId,
                    'jenis_kkn_id' => $jenisKknIdDipilih,
                    'jadwal_kkn_id' => $jadwalKkn->id,
                    'jenis_kkn' => $dataFromSiakad['jenis_kkn'],
                    'status_pendaftaran' => 'pending',
                    'payment_id' => $payment->id,
                ]);
            }

            // 4. Konfigurasi Midtrans (Sebaiknya taruh di Service Provider atau Constructor)
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => (int)$biayaKkn
                ],
                'item_details' => [[
                    'id' => (string) $jenisKknIdDipilih,
                    'price' => (int)$biayaKkn,
                    'quantity' => 1,
                    'name' => $dataFromSiakad['jenis_kkn']
                ]],
                'customer_details' => [
                    'first_name' => $mahasiswaData['name'],
                    'email' => $mahasiswaData['email'] ?? $mahasiswaData['nim'] . '@gmail.com',
                ]
            ];

            try {
                $snapToken = Snap::getSnapToken($midtransParams);
                
                // Update Snap Token ke database
                $payment->update(['snap_token' => $snapToken]);

                return response()->json(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()], 500);
            }
        });
    }

    public function cancelTransaction($orderId) {

        $mahasiswaId = Session::get('mahasiswa_data')['id'];

        $payment = Payment::where('order_id', $orderId)
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->where('status', 'pending')->first();
        
        if (!$payment) {
            return back()->withErrors([
                'error' => 'Transaksi tidak ditemukan.',
            ]);
        }

        if ($payment->status === 'success') {
            return back()->withErrors([
                'error' => 'Transaksi sudah berhasil.',
            ]);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
        return DB::transaction(function () use ($payment) {
            // beritahu Midtrans untuk membatalkan transaksi
            // Ini akan mematikan Snap Token/QRIS agar tidak bisa dibayar lagi
            Transaction::cancel($payment->order_id);

            // Update status Payment di database lokal
            $payment->update([
                'status' => 'failed'
            ]);

            // Update atau Hapus Pendaftaran agar mahasiswa bisa daftar ulang
            // Kita hapus pendaftarannya agar status_kkn di sistem kembali ke "Belum Daftar"
            DB::table('pendaftaran_kkn')
                ->where('payment_id', $payment->id)
                ->delete();

            return redirect()->route('mahasiswa.pembayaran')->with('success', 'Transaksi berhasil dibatalkan.');
        });

    } catch (\Exception $e) {
        // Jika error dari Midtrans (misal: sudah kadaluarsa), kita tetap izinkan pembatalan lokal
        $payment->update(['status' => 'failed']);
        DB::table('pendaftaran_kkn')->where('payment_id', $payment->id)->delete();
        
        return redirect()->route('mahasiswa.pembayaran')->with('success', 'Transaksi dibatalkan.');
    }
    }

    public function riwayatTransaksi()
    {
        $mahasiswaId = Session::get('mahasiswa_data');

        // ambil payment sesuai mahasiswa id
        $payments = Payment::where('mahasiswa_id', $mahasiswaId['id'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $mahasiswa = Mahasiswa::where('id', $mahasiswaId)->first();

        return Inertia::render('Mahasiswa/RiwayatTransaksi', [
            'payments' => $payments,
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function cetakTransaksi($orderId)
    {
        // Ambil ID mahasiswa dari session
        $mahasiswaId = Session::get('mahasiswa_data')['id'];

        $payment = Payment::with('mahasiswa')
            ->where('order_id', $orderId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'success')
            ->firstOrFail();

        $data = [
            'payment' => $payment
        ];

        $pdf = Pdf::loadView('pdf.Mahasiswa.Invoice', $data);

        return $pdf->stream('Invoice-' . $payment->order_id . '.pdf');
    }

    public function cetakPendaftaran($orderId)
    {
        // Ambil ID mahasiswa dari session
        $mahasiswaId = Session::get('mahasiswa_data')['id'];

        $payment = Payment::with('mahasiswa')
            ->where('order_id', $orderId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'success')
            ->firstOrFail();

        $mahasiswa = Mahasiswa::where('id', $mahasiswaId)->first();

        $data = [
            'payment' => $payment,
            'mahasiswa' => $mahasiswa
        ];

        $pdf = Pdf::loadView('pdf.Mahasiswa.Formulir', $data);

        return $pdf->stream('formulir-pendaftaran-' . $mahasiswa->nama . '.pdf');
    }
        
}
