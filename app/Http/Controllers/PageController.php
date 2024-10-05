<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Banner;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use LogsActivity;

    public function listarBanners()
    {
        $banners = Banner::orderBy('created_at', 'DESC')->get();
        return response()->json($banners, 200);
    }

    public function listarSeccion($idPage)
    {
        // Intentar encontrar la página con el idPage proporcionado
        $page = Page::with('archivos')->findOrFail($idPage);

        // Verificar si la página existe
        if (!$page) {
            return response()->json(['message' => 'Sección no encontrada'], 404);
        }

        // Devolver la sección asociada a la página en formato JSON
        return response()->json(['data' => $page], 200);
    }

    // Listar todos los paginas
    public function index()
    {
        $pages = Page::orderBy('created_at', 'DESC')->get();
        return response()->json($pages, 200);
    }

    // Guardar un nuevo banner
    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',  // Validar imagenn
            'estado' => 'in:publicado,oculto',
            'contenido' => 'required|string',
        ]);

        // Subir la imagen al almacenamiento
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('banners', 'public');
        }

        // Crear el pagina
        $page = new Page();
        $page->titulo = $request->input('titulo');
        $page->contenido = $request->input('contenido');
        $page->imagen = Storage::url($imagePath); // Almacenar la URL de la imagen
        $page->estado = $request->input('estado');
        $page->fecha = Carbon::now();
        $page->user_id = Auth::id();
        $page->save();

        $this->logActivity('create_item', 'Usuario registro un página ID :' . $page->id);
        return response()->json(['message' => 'Página registrada con exito!'], 201);
    }

    public function update(Request $request, $idPage)
    {
        // Validación de los datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar la imagen solo si está presente
            'estado' => 'in:publicado,oculto',
            'contenido' => 'required|string',
        ]);

        // Crear el pagina
        $page = Page::findOrFail($idPage);

        // Si hay una nueva imagen, eliminar la anterior y subir la nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($page->imagen) {
                Storage::disk('public')->delete($page->imagen);
            }
            // Subir la nueva imagen
            $imagePath = $request->file('imagen')->store('banners', 'public');
            $page->imagen = Storage::url($imagePath); // Almacenar la URL de la nueva imagen
        }

        $page->titulo = $request->input('titulo');
        $page->contenido = $request->input('contenido');
        $page->estado = $request->input('estado');
        $page->user_id = Auth::id();
        $page->save();

        $this->logActivity('update_item', 'Usuario actualizó la página ID :' . $page->id);
        return response()->json(['message' => 'Página actualizada con éxito!'], 200);
    }
    // Eliminar un pagina
    public function destroy($id)
    {
        $pagina = Page::findOrFail($id);

        // Verificar si el banner tiene una imagen asociada y eliminarla del almacenamiento
        if ($pagina->image) {
            Storage::disk('public')->delete($pagina->image);
        }

        $pagina->delete();

        $this->logActivity('delete_item', 'Usuario eliminó la página ID :' . $pagina->id);
        return response()->json(['message' => 'Página eliminada exitosamente'], 200);
    }

    public function uploadFile(Request $request, $idPage)
    {
        // Validar el archivo y el nombre del archivo
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4|max:300000', // Archivos permitidos y tamaño máximo de 300MB
            'nombre' => 'required|string|max:255', // Validar el nombre del archivo
        ]);

        // Subir el archivo
        if ($request->hasFile('archivo')) {
            // Obtener los detalles del archivo
            $archivoSubido = $request->file('archivo');
            $extension = $archivoSubido->getClientOriginalExtension(); // Obtener la extensión del archivo

            // Generar un nombre único y seguro usando slug
            $slugNombre = Str::slug($request->input('nombre'), '-');
            $customName = $slugNombre . '_' . uniqid() . '.' . $extension; // Generar un nombre único con slug

            // Guardar archivo en la carpeta 'archivos' dentro del almacenamiento público
            $filePath = $archivoSubido->storeAs('archivos', $customName, 'public');

            // Crear registro en la tabla 'archivos'
            $archivo = new Archivo();
            $archivo->nombre = $request->input('nombre'); // Guardar el nombre original
            $archivo->url = Storage::url($filePath); // Guardar la URL pública del archivo
            $archivo->tipo_archivo = $extension; // Guardar el tipo de archivo
            $archivo->tamano = $archivoSubido->getSize(); // Guardar el tamaño en bytes
            $archivo->page_id = $idPage; // Asociar el archivo con la página (asumiendo que hay una relación)
            $archivo->save();
        }

        return response()->json(['message' => 'Archivo subido y guardado con éxito!'], 200);
    }

    public function getFiles($idPage)
    {
        // Validar que el ID de la página sea un número válido
        if (!is_numeric($idPage)) {
            return response()->json(['error' => 'ID de página no válido.'], 400);
        }

        // Obtener los archivos asociados a la página
        $files = Archivo::where('page_id', $idPage)->orderBy('id', 'DESC')->get(['id', 'nombre', 'url', 'tipo_archivo', 'tamano', 'created_at']);

        // Verificar si se encontraron archivos
        if ($files->isEmpty()) {
            return response()->json(['data' => []], 200); // Retorna un arreglo vacío si no hay archivos
        }

        // Retornar los archivos en formato JSON
        return response()->json(['data' => $files], 200);
    }
}
