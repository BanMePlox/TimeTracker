<?php

namespace App\Exports;

use App\Models\Fichaje;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FichajesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Fichaje::with('user')->orderBy('created_at', 'desc');

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $this->filters['fecha_desde']);
        }
        if (!empty($this->filters['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $this->filters['fecha_hasta']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Empleado', 'Email', 'Tipo', 'Fecha', 'Hora'];
    }

    public function map($fichaje): array
    {
        return [
            $fichaje->user->name,
            $fichaje->user->email,
            ucfirst($fichaje->tipo),
            $fichaje->created_at->format('d/m/Y'),
            $fichaje->created_at->format('H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
