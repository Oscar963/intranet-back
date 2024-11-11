<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Retrieve all files, ordered by creation date.
     */
    public function getAllFiles()
    {
        return File::orderBy('created_at', 'DESC')->get();
    }

    /**
     * Store a new file.
     */
    public function createFile(array $data)
    {
        $file = new File();
        $file->name = $data['name'];
        $file->description = $data['description'];
        $file->slug = Str::slug($data['name']);
        $file->created_by = auth()->id();

        if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
            $fileName = pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME) . '-' . uniqid() . '.' . $data['file']->getClientOriginalExtension();
            $filePath = $data['file']->storeAs('files', $fileName, 'public');
            $file->path = url('storage/' . $filePath);
        }

        $file->save();
        return $file;
    }

    /**
     * Retrieve a specific file by ID.
     */
    public function getFileById(int $id)
    {
        return File::findOrFail($id);
    }

    /**
     * Update an existing file.
     */
    public function updateFile(int $id, array $data)
    {
        $file = $this->getFileById($id);

        $file->name = $data['name'];
        $file->description = $data['description'];
        $file->updated_by = auth()->id();

        $file->save();
        return $file;
    }

    /**
     * Delete a file by ID.
     */
    public function deleteFile(int $id)
    {
        $file = $this->getFileById($id);

        if ($file->path) {
            $filePath = str_replace('/storage/', '', $file->path);
            Storage::disk('public')->delete($filePath);
        }

        $file->delete();
    }
}
