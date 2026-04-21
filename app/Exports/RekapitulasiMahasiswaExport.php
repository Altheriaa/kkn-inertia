<?php

namespace App\Exports;

use App\Models\JadwalKkn;
use App\Models\PendaftaranKkn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RekapitulasiMahasiswaExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $statusJadwal;
    protected $statusJenisKkn;
    protected $namaPeriode;
    protected $selectedJenisKkn;

    public function __construct($statusJadwal, $statusJenisKkn, $namaPeriode, $selectedJenisKkn)
    {
        $this->statusJadwal     = $statusJadwal;
        $this->statusJenisKkn   = $statusJenisKkn;
        $this->namaPeriode      = $namaPeriode;
        $this->selectedJenisKkn = $selectedJenisKkn;
    }

    public function collection()
    {
        $query = PendaftaranKkn::with(['mahasiswa', 'kelompokKkn', 'jadwalKkn'])
            ->where('status_pendaftaran', 'valid');

        if ($this->statusJadwal) {
            $query->where('jadwal_kkn_id', $this->statusJadwal);
        }

        if ($this->statusJenisKkn) {
            $query->where('jenis_kkn_id', $this->statusJenisKkn);
        }

        $pendaftarans = $query->orderBy('created_at', 'desc')->get();

        return $pendaftarans->map(function ($item, $index) {
            return [
                'no'           => $index + 1,
                'nim'          => $item->mahasiswa?->nim ?? '-',
                'nama'         => $item->mahasiswa?->nama ?? '-',
                'fakultas'     => $item->mahasiswa?->fakultas ?? '-',
                'prodi'        => $item->mahasiswa?->prodi ?? '-',
                'jenis_kelamin'=> $item->mahasiswa?->jenis_kelamin ?? '-',
                'no_hp'        => $item->mahasiswa?->no_hp ?? '-',
                'kelompok'     => $item->kelompok_kkn?->nama_kelompok ?? 'Belum Ditempatkan',
                'periode'      => $item->jadwal_kkn?->nama_periode ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama Mahasiswa',
            'Fakultas',
            'Program Studi',
            'L/P',
            'No. HP',
            'Kelompok KKN',
            'Periode KKN',
        ];
    }

    public function title(): string
    {
        return 'Rekap Mahasiswa KKN';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // Header row styling (row 1)
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1B5E20'], // dark green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFFFFFFF'],
                ],
            ],
        ]);

        // Data rows
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Zebra striping
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFE8F5E9'], // light green
                        ],
                    ]);
                }
            }
        }

        // Row height
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Center "No" and "L/P" columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
