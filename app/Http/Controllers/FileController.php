<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Http\Resources\FileResource;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class FileController extends Controller
{
    use LogsActivity;

    protected  $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $file = $this->fileService->updateFile($id, $validatedData);
            $this->logActivity('update_role', 'Usuario actualizó el archivo con ID: ' . $file->id);
            return response()->json(['message' => 'Archivo actualizado con éxito!', 'data' => new FileResource($file)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar el archivo.'], 500);
        }
    }
    /**
     * Remove the specified file.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->fileService->deleteFile($id);
            $this->logActivity('delete_file', 'Usuario eliminó el archivo con ID: ' . $id);
            return response()->json(['message' => 'Archivo eliminado con éxito'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar el archivo.' . $e->getMessage()], 500);
        }
    }
}
