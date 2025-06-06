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

    public function getAllPagesByQuery(?string $query, int $perPage = 15)
    {
        $queryBuilder = Page::orderBy('created_at', 'DESC');

        if ($query) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%");
            });
        }
        return $queryBuilder->paginate($perPage);
    }

    public function createPage(array $data)
    {
        $page = new Page();
        $page->title = $data['title'];
        $page->content = $data['content'];
        $page->slug = Str::slug($data['title']);
        $page->status = $data['status'];

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $imageName = pathinfo($data['image']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
            // Almacenar la imagen en el disco 'public' con el nombre generado
            $imagePath = $data['image']->storeAs('pages/image', $imageName, 'public');
            $page->image = url('storage/' . $imagePath); // Asignar la URL completa
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

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($page->image) {
                $previousImagePath = str_replace('/storage/', '', $page->image);
                Storage::disk('public')->delete($previousImagePath);
            }
            $imageName = Str::slug($data['title']) . '-' . time() . '.' . $data['image']->getClientOriginalExtension();
            $imagePath = $data['image']->storeAs('pages/image', $imageName, 'public');

            // Asignar la URL completa a la propiedad image
            $page->image = url('storage/' . $imagePath);
        }

        $page->date = now();
        $page->updated_by = auth()->id();
        $page->save();
        return $page;
    }

    public function deletePage($id)
    {
        $page = $this->getPageById($id);

        if ($page->image) {
            Storage::disk('public')->delete($page->image);
        }
        $page->deleted_by = auth()->id();
        $page->save();
        $page->delete();
    }

    public function uploadFile(array $data)
    {
        $file = new File();
        $file->name = $data['name'];
        $file->description = $data['description'];

        if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
            $file->type = $data['file']->getClientMimeType();
            $file->size = $data['file']->getSize();
            // Generar un nombre único para el archivo
            $fileName = pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['file']->getClientOriginalExtension();
            $filePath = $data['file']->storeAs('files/pages', $fileName, 'public');
        }
        // Asignar la URL completa del archivo
        $file->url = url('storage/' . $filePath);

        $file->created_by = auth()->id();
        $file->save();
        $file->pages()->attach($data['page_id']);
        return $file;
    }

    public function getAllFiles($slug)
    {
        $page = $this->getPageBySlug($slug);
        return File::where('page_id', $page->id)->orderBy('created_at', 'DESC')->get();
    }

    public function getAllFilesByQuery(?int $idpage = null, ?string $query = null, int $perPage = 15)
    {
        return File::orderBy('created_at', 'DESC')
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                });
            })
            ->when($idpage, function ($queryBuilder) use ($idpage) {
                $queryBuilder->whereHas('pages', function ($q) use ($idpage) {
                    $q->where('pages.id', $idpage);
                });
            })
            ->paginate($perPage);
    }
}
