<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Formulir Pendaftaran KKN - {{ $mahasiswa->nama }}</title>
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
            margin-top: 10px;
            border-bottom: 2px solid #0d6b38;
            padding-bottom: 10px;
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

        .form-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #0d6b38;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title {
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
            background-color: #0d6b38;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
        }

        .info-table {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .info-table td {
            padding: 4px 6px;
            font-size: 13px;
            vertical-align: top;
        }

        .info-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .info-table .label {
            width: 30%;
            font-weight: bold;
            color: #444;
        }

        .statement {
            margin-top: 5px;
            line-height: 1.4;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #fafafa;
            page-break-inside: avoid;
        }

        .signature {
            width: 100%;
            margin-top: 20px;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature td {
            padding-top: 20px;
            font-size: 13px;
            width: 50%;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #888;
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

        <div class="form-title">FORMULIR PENDAFTARAN KKN</div>

        {{-- Informasi Mahasiswa --}}
        <div class="section-title">A. DATA MAHASISWA</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: {{ $mahasiswa->nama }}</td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td>: {{ $mahasiswa->nim }}</td>
            </tr>
            <tr>
                <td class="label">Fakultas</td>
                <td>: {{ $mahasiswa->fakultas }}</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td>: {{ $mahasiswa->prodi ?? }}</td>
            </tr>
            <tr>
                <td class="label">Nomor HP / WhatsApp</td>
                <td>: {{ $mahasiswa->no_hp }}</td>
            </tr>
        </table>

        {{-- Informasi Pendaftaran KKN --}}
        <div class="section-title">B. DATA PENDAFTARAN</div>
        <table class="info-table">
            <tr>
                <td class="label">Jenis KKN</td>
                <td>: <strong>{{ $payment->jenis_kkn }}</strong></td>
            </tr>
            <tr>
                <td class="label">Status Pencalonan</td>
                <td>: <span style="font-weight: bold; color: #0d6b38;">TERDAFTAR</span>
                    ({{ strtoupper($payment->status) == 'SUCCESS' ? 'LUNAS' : strtoupper($payment->status) }})</td>
            </tr>
            <tr>
                <td class="label">No. Referensi (Order ID)</td>
                <td>: {{ $payment->order_id }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Pendaftaran</td>
                <td>: {{ $payment->updated_at->format('d F Y H:i') }}</td>
            </tr>
        </table>

        {{-- Pernyataan --}}
        <div class="section-title">C. PERNYATAAN MAHASISWA</div>
        <div class="statement">
            Saya yang bertanda tangan di bawah ini menyatakan bahwa data yang saya isikan pada formulir pendaftaran KKN
            adalah benar dan dapat dipertanggungjawabkan. Saya bersedia mengikuti seluruh peraturan dan tata tertib,
            serta rangkaian kegiatan KKN sesuai ketentuan yang berlaku di Universitas Abulyatama.
        </div>

        {{-- Tanda tangan --}}
        <table class="signature">
            <tr>
                <td></td>
                <td>
                    Aceh Besar, {{ date('d F Y') }}<br><br>
                    Mahasiswa Pendaftar,<br><br><br><br><br>
                    <strong>( {{ $mahasiswa->nama }} )</strong><br>
                    NIM. {{ $mahasiswa->nim }}
                </td>
            </tr>
        </table>

        <div class="footer">
            Dokumen ini dicetak secara otomatis dari Sistem Informasi KKN Universitas Abulyatama.
        </div>
    </div>
</body>

</html>