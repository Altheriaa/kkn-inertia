<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $payment->order_id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 13px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-top td {
            vertical-align: top;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #0d6b38;
            width: 60%;
        }

        .logo-container {
            width: 40%;
            text-align: right;
        }

        .logo-container img {
            max-height: 40px;
        }

        .header-details {
            margin-top: 20px;
        }

        .header-details td {
            vertical-align: top;
        }

        .company-address {
            width: 60%;
            line-height: 1.5;
        }

        .bank-details {
            width: 40%;
            line-height: 1.5;
        }

        .invoice-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #0d6b38;
            margin: 40px 0;
            letter-spacing: 1px;
        }

        .customer-section {
            margin-bottom: 20px;
        }

        .customer-details {
            width: 50%;
            vertical-align: top;
            line-height: 1.5;
        }

        .date-details {
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .date-table {
            width: auto;
            float: right;
        }

        .date-table td {
            text-align: left;
            padding: 2px 10px;
            font-size: 12px;
        }

        .date-label {
            font-weight: bold;
        }

        .table-items {
            width: 100%;
            margin-top: 20px;
            border-bottom: 1px solid #eee;
        }

        .table-items th {
            background-color: #0d6b38;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        .table-items td {
            padding: 15px 10px;
            vertical-align: top;
        }

        .align-right {
            text-align: right !important;
        }

        .align-center {
            text-align: center !important;
        }

        .totals {
            margin-top: 30px;
            width: 100%;
        }

        .totals-table {
            width: 50%;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 10px 10px;
        }

        .totals-table tr {
            border-bottom: 1px solid #eee;
        }

        .totals-table tr:last-child {
            border-bottom: none;
        }

        .text-theme {
            color: #0d6b38;
        }

        .total-row {
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 150px;
            text-align: center;
            font-size: 10px;
            color: #888;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="container">

        <table class="header-top">
            <tr>
                <td class="company-name">Panitia Pelaksana KKN Universitas Abulyatama</td>
                <td class="logo-container">
                    <img src="{{ public_path('assets/img/Unaya.png') }}" alt="Logo">
                </td>
            </tr>
        </table>

        <table class="header-details">
            <tr>
                <td class="company-address">
                    Jalan Jl. Blangbintang Lama No.KM 8,<br>
                    RW.5, Lampoh Keude, Kec. Kuta Baro,<br>
                    Kabupaten Aceh Besar, Aceh 24415<br>
                    Telepon: -
                </td>
                <td class="bank-details">
                    Email: kkn@unaya.ac.id<br>
                    Web: kkn.unaya.ac.id
                </td>
            </tr>
        </table>

        <div class="invoice-title">
            BUKTI PEMBAYARAN KKN<br>
            <span style="font-size: 16px; color: #555;"># {{ $payment->order_id }}</span>
        </div>

        <table class="customer-section">
            <tr>
                <td class="customer-details">
                    <strong style="font-size: 14px; text-transform: uppercase;">Data Mahasiswa</strong><br><br>
                    <strong>{{ strtoupper($payment->mahasiswa->nama) }}</strong><br>
                    NIM: {{ $payment->mahasiswa->nim }}
                </td>
                <td class="date-details">
                    <table class="date-table">
                        <tr>
                            <td class="date-label">TANGGAL BAYAR:</td>
                            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="date-label">STATUS:</td>
                            <td style="color: #0d6b38; font-weight: bold;">LUNAS</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="table-items">
            <thead>
                <tr>
                    <th style="text-align: left; width: 50%;">URAIAN PEMBAYARAN</th>
                    <th style="text-align: center; width: 10%;">JUMLAH</th>
                    <th style="text-align: right; width: 40%;">TOTAL BIAYA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Biaya Pendaftaran {{ $payment->jenis_kkn }}</td>
                    <td class="align-center">1</td>
                    <td class="align-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td class="text-theme">SUBTOTAL</td>
                    <td class="align-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-theme">BIAYA ADMIN</td>
                    <td class="align-right">Rp 0</td>
                </tr>
                <tr class="total-row">
                    <td class="text-theme">TOTAL DIBAYAR</td>
                    <td class="align-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Ini adalah bukti pembayaran yang sah dan diterbitkan oleh sistem secara otomatis.
            <br>
            &copy; {{ date('Y') }} Panitia KKN Universitas Abulyatama.
        </div>
    </div>
</body>

</html>