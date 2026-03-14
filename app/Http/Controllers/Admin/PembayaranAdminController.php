<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Mahasiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class PembayaranAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $payments = Payment::with('mahasiswa')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($search) {
                        $m->where('nama', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return Inertia::render("Admin/RiwayatTransaksi", [
            'payments' => $payments,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function cetakTransaksi($orderId)
    {

        $payment = Payment::with('mahasiswa')
            ->where('order_id', $orderId)
            ->where('status', 'success')
            ->firstOrFail();

        $data = [
            'payment' => $payment
        ];

        $pdf = Pdf::loadView('pdf.Admin.Invoice', $data);

        return $pdf->stream('bukti-pembayaran-' . $payment->order_id . '.pdf');
    }

    public function cetakPendaftaran($orderId)
    {
        $payment = Payment::with('mahasiswa')
            ->where('order_id', $orderId)
            ->where('status', 'success')
            ->firstOrFail();

        $mahasiswa = $payment->mahasiswa;

        // Optional: Cek jaga-jaga jika mahasiswanya sudah terhapus di database
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        $data = [
            'payment' => $payment,
            'mahasiswa' => $mahasiswa
        ];

        $pdf = Pdf::loadView('pdf.Admin.Formulir', $data);

        return $pdf->stream('formulir-pendaftaran-' . $mahasiswa->nama . '.pdf');
    }
}
