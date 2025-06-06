<?php

namespace App\Http\Controllers;

use App\Http\Requests\MobileRequest;
use App\Http\Resources\MobileResource;
use App\Models\Mobile;
use App\Services\MobileService;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MobileImport;
use App\Exports\MobileExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use Throwable;

class MobileController extends Controller
{
    use LogsActivity;

    protected $mobileService;

    public function __construct(MobileService $mobileService)
    {
        $this->mobileService = $mobileService;
    }

    /**
     * Listar todos los móviles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $request->query('q'); // Parámetro de búsqueda
            $perPage = $request->query('show');
            $banners = $this->mobileService->getAllMobilesByQuery($query, $perPage);
            return response()->json(['data' =>  MobileResource::collection($banners)->response()->getData(true)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener los mobiles.' . $e->getMessage()], 500);
        }
    }

    /**
     * Guardar un nuevo móvil.
     */
    public function store(MobileRequest $request): JsonResponse
    {
        try {
            $mobile = $this->mobileService->createMobile($request->validated());
            $this->logActivity('create_mobile', 'Usuario creó un móvil con ID: ' . $mobile->id);

            return response()->json(['message' => 'Móvil guardado exitosamente', 'data' => new MobileResource($mobile)], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al guardar el móvil. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un móvil.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $mobile = $this->mobileService->getMobileById($id);
            return response()->json(['data' => new MobileResource($mobile)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Móvil no encontrado.'], 404);
        }
    }

    /**
     * Actualizar un móvil.
     */
    public function update(int $id, MobileRequest $request): JsonResponse
    {
        try {
            $updatedMobile = $this->mobileService->updateMobile($id, $request->validated());
            $this->logActivity('update_mobile', 'Usuario actualizó el móvil con ID: ' . $updatedMobile->id);

            return response()->json(['message' => 'Móvil actualizado exitosamente', 'data' => new MobileResource($updatedMobile)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar el móvil. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un móvil.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->mobileService->deleteMobile($id);
            $this->logActivity('delete_mobile', 'Usuario eliminó el móvil con ID: ' . $id);
            return response()->json(['message' => 'Móvil eliminado exitosamente'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar el móvil. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Importar datos de móviles desde un archivo Excel.
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv,xls|max:10240',
            ]);

            Mobile::truncate();
            Excel::import(new MobileImport, $request->file('file'));

            return response()->json([
                'message' => 'Datos importados correctamente.',
            ], 200);
        } catch (ValidationException $e) {
            // Errores de validación del request
            return response()->json([
                'message' => 'El archivo no es válido.',
                'errors' => $e->errors(),
            ], 422);
        } catch (ExcelValidationException $e) {
            // Errores internos de validación del Excel
            return response()->json([
                'message' => 'El archivo contiene datos inválidos.',
                'errors' => $e->failures(),
            ], 422);
        } catch (Throwable $e) {
            // Registra el error completo para desarrolladores
            Log::error('Error al importar anexos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Devuelve un mensaje genérico al cliente
            return response()->json([
                'message' => 'Error al importar los datos. Verifique que el archivo tenga el formato correcto.',
            ], 500);
        }
    }

    /**
     * Exportar los móviles en formato Excel.
     */
    public function export(): BinaryFileResponse
    {
        $this->logActivity('download_file', 'Usuario exporto el excel de móviles');
        return Excel::download(new MobileExport, 'mobiles.xlsx');
    }
}
