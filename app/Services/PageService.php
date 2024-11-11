<?php

namespace App\Services;

use App\Models\File;
use App\Models\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageService
{
    public function getAllPages()
    {
        return Page::orderBy('created_at', 'DESC')->get();
    }

    public function createPage(array $data)
    {
        $page = new Page();
        $page->title = $data['title'];
        $page->content = $data['content'];
        $page->slug = Str::slug($data['title']);
        $page->status = $data['status'];

        if (isset($data['image_menu']) && $data['image_menu'] instanceof \Illuminate\Http\UploadedFile) {
            $imageName = pathinfo($data['image_menu']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['image_menu']->getClientOriginalExtension();
            // Almacenar la imagen en el disco 'public' con el nombre generado
            $imagePath = $data['image_menu']->storeAs('pages/image_menu', $imageName, 'public');
            $page->image_menu = url('storage/' . $imagePath); // Asignar la URL completa
        }

        if (isset($data['image_banner']) && $data['image_banner'] instanceof \Illuminate\Http\UploadedFile) {
            $imageName = pathinfo($data['image_banner']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['image_banner']->getClientOriginalExtension();
            // Almacenar la imagen en el disco 'public' con el nombre generado
            $imagePath = $data['image_banner']->storeAs('pages/image_banner', $imageName, 'public');
            $page->image_banner = url('storage/' . $imagePath); // Asignar la URL completa
        }

        $page->date = now();
        $page->created_by = auth()->id();
        $page->save();
        return $page;
    }

    public function getPageById($id)
    {
        return Page::findOrFail($id);
    }

    public function getPageBySlug($slug)
    {
        return Page::where('slug', $slug)->firstOrFail();
    }

    public function updatePage($id, array $data)
    {
        $page = $this->getPageById($id);

        $page->title = $data['title'];
        $page->content = $data['content'];
        $page->slug = Str::slug($data['title']);
        $page->status = $data['status'];

        if (isset($data['image_menu']) && $data['image_menu'] instanceof \Illuminate\Http\UploadedFile) {
            if ($page->image_menu) {
                // Obtener el nombre del archivo anterior para eliminarlo
                $previousImagePath = str_replace('/storage/', '', $page->image_menu);
                Storage::disk('public')->delete($previousImagePath);
            }
            $imageName = pathinfo($data['image_menu']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['image_menu']->getClientOriginalExtension();
            $imagePath = $data['image_menu']->storeAs('pages/image_menu', $imageName, 'public');

            // Asignar la URL completa a la propiedad image
            $page->image_menu = url('storage/' . $imagePath);
        }

        if (isset($data['image_banner']) && $data['image_banner'] instanceof \Illuminate\Http\UploadedFile) {
            if ($page->image_banner) {
                $previousImagePath = str_replace('/storage/', '', $page->image_banner);
                Storage::disk('public')->delete($previousImagePath);
            }
            $imageName = Str::slug($data['title']) . '-' . time() . '.' . $data['image_banner']->getClientOriginalExtension();
            $imagePath = $data['image_banner']->storeAs('pages/image_banner', $imageName, 'public');

            // Asignar la URL completa a la propiedad image
            $page->image_banner = url('storage/' . $imagePath);
        }

        $page->date = now();
        $page->updated_by = auth()->id();
        $page->save();
        return $page;
    }

    public function deletePage($id)
    {
        $page = $this->getPageById($id);

        if ($page->image_menu) {
            Storage::disk('public')->delete($page->image_menu);
        }
        if ($page->image_banner) {
            Storage::disk('public')->delete($page->image_banner);
        }
        $page->deleted_by = auth()->id();
        $page->save();
        $page->delete();
    }

    public function uploadFile(array $data)
    {
        $page = $this->getPageBySlug($data['slug']);

        $file = new File();
        $file->name = $data['name'];
        $file->description = $data['description'];
        $file->type = $data['file']->getClientMimeType();
        $file->size = $data['file']->getSize();
        $file->page_id = $page->id;
        $file->created_by = auth()->id();

        if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
            // Generar un nombre único para el archivo
            $fileName = pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['file']->getClientOriginalExtension();
            $filePath = $data['file']->storeAs('files/pages', $fileName, 'public');
        }
        // Asignar la URL completa del archivo
        $file->url = url('storage/' . $filePath);
        $file->save();
        return $file;
    }

    public function getAllFiles($slug)
    {
        $page = $this->getPageBySlug($slug);
        return File::where('page_id', $page->id)->orderBy('created_at', 'DESC')->get();
    }
}
