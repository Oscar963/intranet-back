<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class ArchivoController extends Controller
{
    use LogsActivity;

    public function destroy($id)
    {
        // Buscar el archivo por ID
        $archivo = Archivo::find($id);

        // Verificar si el archivo existe
        if (!$archivo) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        // Eliminar el archivo del almacenamiento
        Storage::disk('public')->delete(str_replace('/storage/', '', $archivo->url));

        // Eliminar el registro del archivo en la base de datos
        $archivo->delete();

        // Lógica para registrar la actividad de eliminación
        $this->logActivity('delete_item', 'Usuario eliminó archivo ID :' . $archivo->id);

        return response()->json(['message' => 'Archivo eliminado con éxito'], 204);
    }
}
