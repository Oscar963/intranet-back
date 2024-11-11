<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use App\Http\Requests\PageRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\PageResource;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class PageController extends Controller
{
    use LogsActivity;

    protected  $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * List all pages.
     */
    public function index(): JsonResponse
    {
        try {
            $pages = $this->pageService->getAllPages();
            return response()->json(['data' => PageResource::collection($pages)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener las páginas.'], 500);
        }
    }

    /**
     * Store a newly created page.
     */
    public function store(PageRequest $request): JsonResponse
    {
        try {
            $page = $this->pageService->createPage($request->validated());
            $this->logActivity('create_page', 'Usuario creó una página con ID: ' . $page->id);
            return response()->json(['message' => 'Página creada con éxito!', 'data' => new PageResource($page)], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al crear la página.'], 500);
        }
    }

    /**
     * Show a specific page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $page = $this->pageService->getPageBySlug($slug);
            return response()->json(['data' => new PageResource($page)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Página no encontrada.'], 404);
        }
    }

    /**
     * Update the specified page.
     */
    public function update(PageRequest $request, int $id): JsonResponse
    {
        try {
            $page = $this->pageService->updatePage($id, $request->validated());
            $this->logActivity('update_page', 'Usuario actualizó la página con ID: ' . $page->id);
            return response()->json(['message' => 'Página actualizada con éxito!', 'data' => new PageResource($page)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar la página.'], 500);
        }
    }

    /**
     * Remove the specified page.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->pageService->deletePage($id);
            $this->logActivity('delete_page', 'Usuario eliminó la página con ID: ' . $id);
            return response()->json(['message' => 'Página eliminada con éxito'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al eliminar la página.'], 500);
        }
    }

    /**
     * Upload files through CKEditor.
     */
    protected function storeCkEditor(Request $request): JsonResponse
    {
        try {
            $file = $request->file('upload');
            $imagePath = $file->store('pages/ckeditor', 'ckeditor_images');
            $imageUrl = Storage::disk('ckeditor_images')->url($imagePath);

            // Return URL in the format expected by CKEditor
            return response()->json(['url' => $imageUrl], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al subir la imagen.'], 500);
        }
    }

    protected function storeFile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,jpg,jpeg,png,gif,bmp|max:20480', // 20MB de límite
            'slug' => 'required|string|max:255',
        ]);

        try {
            $file = $this->pageService->uploadFile($request->all());
            $this->logActivity('upload_file', 'Usuario subió un archivo con ID: ' . $file->id);

            return response()->json([
                'message' => 'Archivo subido con éxito!',
                'data' => new FileResource($file)
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al subir el archivo. '], 500);
        }
    }

    protected function getFiles($slug): JsonResponse
    {
        try {
            $file = $this->pageService->getAllFiles($slug);
            return response()->json(['data' => FileResource::collection($file)], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener los archivos.'], 500);
        }
    }
}
