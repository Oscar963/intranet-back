<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerService
{
    public function getAllBanners()
    {
        return Banner::orderBy('created_at', 'DESC')->get();
    }

    public function createBanner(array $data)
    {
        $banner = new Banner();
        $banner->title = $data['title'];
        $banner->date = now();
        $banner->date_expiration = $data['date_expiration'];
        $banner->status = $data['status'];
        $banner->created_by = auth()->id();

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $imageName = Str::slug($data['title']) . '-' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
            $imagePath = $data['image']->storeAs('banners', $imageName, 'public');
            $banner->image = url('storage/' . $imagePath);
        }

        $banner->save();
        return $banner;
    }

    public function getBannerById($id)
    {
        return Banner::findOrFail($id);
    }

    public function updateBanner($id, array $data)
    {
        $banner = $this->getBannerById($id);

        $banner->title = $data['title'];
        $banner->date = now();
        $banner->date_expiration = $data['date_expiration'];
        $banner->status = $data['status'];
        $banner->updated_by = auth()->id();

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($banner->image) {
                $previousImagePath = str_replace('/storage/', '', $banner->image);
                Storage::disk('public')->delete($previousImagePath);
            }
            $imageName = Str::slug($data['title']) . '-' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
            $imagePath = $data['image']->storeAs('banners', $imageName, 'public');
            $banner->image = url('storage/' . $imagePath);
        }

        $banner->save();
        return $banner;
    }

    public function deleteBanner($id)
    {
        $banner = $this->getBannerById($id);

        if ($banner->image) {
            $imagePath = str_replace('/storage/', '', $banner->image);
            Storage::disk('public')->delete($imagePath);
        }

        $banner->deleted_by = auth()->id();
        $banner->save();
        $banner->delete();
    }
}