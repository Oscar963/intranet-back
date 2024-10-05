<?php

namespace App\Exports;

use App\Models\Registro;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegistrosPdfExport
{
    public function exportar($idRegistro)
    {
        $registro = Registro::with('vehiculo')->find($idRegistro);

        if (!$registro) {
            abort(404, 'Registro no encontrado');
        }

        $pdf = Pdf::loadView('pdf.ficha', compact('registro'));
        return $pdf->download('registro.pdf');
    }
}
