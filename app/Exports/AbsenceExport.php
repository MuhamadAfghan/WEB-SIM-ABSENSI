<?php

namespace App\Exports;

use App\Models\Absence;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class AbsenceExport implements FromQuery, WithHeadings, WithMapping, WithStyles

{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function query()
    {
        return Absence::query()
            ->whereBetween('date-start', [$this->start_date, $this->end_date])
            ->with('user');
    }
    public function headings(): array
    {
        return [
            'ID',
            'Nama Karyawan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Tipe Kehadiran',
            'Status Persetujuan', // Disetujui / Ditolak
            'Keterangan',
            'File Attachment'
        ];
    }
    public function map($absence): array
    {
        return [
            $absence->id,
            $absence->user->name ?? 'N/A',
            $absence->{'date-start'},
            $absence->{'date-end'},
            $absence->type,
            $absence->is_approved ? 'Disetujui' : 'Ditolak',
            $absence->description ?? '-',
            $absence->upload_attachment ? url('storage/' . $absence->upload_attachment) : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        foreach (range('A', 'H') as $column) {
            $sheet->getStyle($column)->getAlignment()->setWrapText(true);
        }
        
        // Style for header row
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4B5563'],
            ],
        ]);

         // Style for all cells
        $sheet->getStyle('A1:H' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Custom style for description column
        $sheet->getStyle('G2:G' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        
        // Custom style for link column
        $sheet->getStyle('H2:H' . $sheet->getHighestRow())->applyFromArray([
            'font' => [
                'color' => ['rgb' => '0000FF'],
                'underline' => true,
            ],
        ]);

        return $sheet;
    }

    //add column width
    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 25, // Nama Karyawan
            'C' => 15, // Tanggal Mulai
            'D' => 15, // Tanggal Selesai
            'E' => 20, // Tipe Kehadiran
            'F' => 20, // Status Persetujuan
            'G' => 35, // Keterangan (dengan word wrap)
            'H' => 50, // File Link (dengan underline)
        ];
    }
}
