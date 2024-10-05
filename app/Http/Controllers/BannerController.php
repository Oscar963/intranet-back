<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Listar todos los banners
    public function index()
    {
        $banners = Banner::orderBy('created_at', 'DESC')->get();
        return response()->json($banners, 200);
    }

    // Guardar un nuevo banner
    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',  // Validar imagenn
            'estado' => 'in:publicado,oculto',
        ]);

        // Subir la imagen al almacenamiento
        if ($request->hasFile('imagen')) {
            $imagePath = $request->file('imagen')->store('banners', 'public');
        }

        // Crear el banner
        $banner = new Banner();
        $banner->titulo = $request->input('titulo');
        $banner->url = $request->input('url');
        $banner->imagen = Storage::url($imagePath); // Almacenar la URL de la imagen
        $banner->estado = $request->input('estado');  // Valor por defecto 'oculto'
        $banner->fecha = Carbon::now();
        $banner->user_id = Auth::id();
        $banner->save();

        return response()->json(['message' => 'Banner guardado exitosamente'], 201);
    }

    // Actualizar un banner
    public function update(Request $request, $idBanner)
    {
        // Validación de los datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar la imagen solo si está presente
            'estado' => 'in:publicado,oculto',
        ]);

        // Buscar el banner a actualizar
        $banner = Banner::findOrFail($idBanner);

        // Si hay una nueva imagen, eliminar la anterior y subir la nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($banner->imagen) {
                Storage::disk('public')->delete($banner->imagen);
            }
            // Subir la nueva imagen
            $imagePath = $request->file('imagen')->store('banners', 'public');
            $banner->imagen = Storage::url($imagePath); // Almacenar la URL de la nueva imagen
        }

        // Actualizar los demás campos
        $banner->titulo = $request->input('titulo');
        $banner->url = $request->input('url');
        $banner->estado = $request->input('estado');  // Valor por defecto 'oculto'
        $banner->fecha = Carbon::now();  // Actualizar la fecha

        // Guardar los cambios
        $banner->save();

        return response()->json(['message' => 'Banner actualizado exitosamente'], 200);
    }

    // Eliminar un banner
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Verificar si el banner tiene una imagen asociada y eliminarla del almacenamiento
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner eliminado exitosamente'], 200);
    }
}
