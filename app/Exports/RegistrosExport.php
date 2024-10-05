<?php

namespace App\Exports;

use App\Models\Registro;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RegistrosExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithEvents
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Registro::with('vehiculo');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('vehiculo', function ($query) use ($search) {
                $query->where('patente', 'LIKE', "%{$search}%")
                    ->orWhere('tipo', 'LIKE', "%{$search}%")
                    ->orWhere('marca', 'LIKE', "%{$search}%")
                    ->orWhere('modelo', 'LIKE', "%{$search}%")
                    ->orWhere('color', 'LIKE', "%{$search}%")
                    ->orWhere('nombre', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($this->filters['status']) && $this->filters['status'] != 0) {
            $status = $this->filters['status'];
            $query->where('estado', $status);
        }

        if (!empty($this->filters['date_filter'])) {
            $dateFilter = $this->filters['date_filter'];
            switch ($dateFilter) {
                case '1':
                    $query->whereDate('entrada', Carbon::today());
                    break;
                case '2':
                    $query->whereDate('entrada', Carbon::yesterday());
                    break;
                case '3':
                    $query->whereBetween('entrada', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case '4':
                    $query->whereMonth('entrada', Carbon::now()->month);
                    break;
                case 'all':
                    // No agregar filtro, mostrar todo
                    break;
                default:
                    // No agregar filtro si el valor no es válido
                    break;
            }
        }

        $query->orderBy('created_at', 'DESC');

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patente',
            'Tipo',
            'Marca',
            'Modelo',
            'Color',
            'Nombre',
            'Fecha de Entrada',
            'Estado'
        ];
    }

    public function map($registro): array
    {
        return [
            $registro->id,
            $registro->vehiculo->patente,
            $registro->vehiculo->tipo,
            $registro->vehiculo->marca,
            $registro->vehiculo->modelo,
            $registro->vehiculo->color,
            $registro->vehiculo->nombre,
            Carbon::parse($registro->entrada)->format('d-m-Y'),
            $registro->estado == 1 ? 'Ingresado' : ($registro->estado == 2 ? 'Salida' : 'Desconocido'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Formato de fecha para la columna H
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo de las cabeceras
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF002060']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Ajustar el ancho de las columnas automáticamente
                foreach (range('A', $event->sheet->getHighestColumn()) as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                // Agregar borde a las cabeceras
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Fijar la primera fila como cabecera
                $event->sheet->freezePane('A2');
            },
        ];
    }
}
