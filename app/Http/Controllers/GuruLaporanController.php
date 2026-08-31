<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GuruLaporanController extends Controller
{
    // Method halaman utama laporan (yang sudah Anda miliki)
    public function index(Request $request)
    {
        $kelas = Kelas::all();
        
        $siswa = Siswa::with('kelas')
            ->when($request->search, function($query, $search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
            })
            ->when($request->kelas_id && $request->kelas_id != 'all', function($query) use ($request) {
                $query->where('kelas_id', $request->kelas_id);
            })
            ->paginate(15)
            ->withQueryString();

        return view('guru.laporan.index', compact('siswa', 'kelas'));
    }

    // Method Baru untuk Export Excel
    public function exportExcel(Request $request)
    {
        // Ambil data dengan filter yang sama persis seperti di halaman index (tanpa pagination)
        $siswa = Siswa::with('kelas')
            ->when($request->search, function($query, $search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
            })
            ->when($request->kelas_id && $request->kelas_id != 'all', function($query) use ($request) {
                $query->where('kelas_id', $request->kelas_id);
            })
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Laporan Poin');

        // Pastikan gridlines terlihat
        $sheet->setShowGridlines(true);

        // Header Judul Laporan
        $sheet->setCellValue('A1', 'LAPORAN AKHIR POIN SISWA - BINTANG POIN');
        $sheet->setCellValue('A2', 'SDIT Nurul Fikri | Dicetak pada: ' . date('d-m-Y H:i'));
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('065F46');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('6B7280');

        // Header Kolom Tabel (Baris ke-4)
        $headers = ['No', 'NIS / NISN', 'Nama Siswa', 'Kelas', 'Sisa Poin Aktif', 'Predikat Karakter'];
        $sheet->fromArray($headers, NULL, 'A4');

        // Styling Header Tabel
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]]
        ];
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // Masukkan Data Siswa
        $rowNumber = 5;
        foreach ($siswa as $index => $item) {
            $poin = $item->poin_saat_ini ?? 0;
            
            // Logika predikat sama seperti di Blade
            if ($poin >= 250) {
                $predikat = 'Sangat Baik';
            } elseif ($poin >= 100) {
                $predikat = 'Baik';
            } else {
                $predikat = 'Perlu Pembinaan';
            }

            $sheet->setCellValue('A' . $rowNumber, $index + 1);
            $sheet->setCellValueExplicit('B' . $rowNumber, $item->nis ?? $item->nisn ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowNumber, $item->nama_lengkap);
            $sheet->setCellValue('D' . $rowNumber, $item->kelas->nama_kelas ?? '-');
            $sheet->setCellValue('E' . $rowNumber, $poin);
            $sheet->setCellValue('F' . $rowNumber, $predikat);

            // Styling baris data
            $isEven = ($rowNumber % 2 == 0);
            $rowFill = $isEven ? 'F4FBF7' : 'FFFFFF';

            $sheet->getStyle("A{$rowNumber}:F{$rowNumber}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowFill]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'font' => ['size' => 10]
            ]);

            // Alignment khusus kolom tertentu
            $sheet->getStyle("A{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$rowNumber}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNumber++;
        }

        // Auto-fit column widths
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output file download
        $fileName = 'Laporan_Poin_Siswa_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}