<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'status' => $this->status,
            'image_menu' => $this->image_menu,
            'image_banner' => $this->image_banner,
            'date' => $this->date ? Carbon::parse($this->date)->format('d-m-Y H:i:s') : null,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
            'deleted_by' => $this->deleted_by ? $this->deleted_by->format('d-m-Y H:i:s') : null,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => new UserResource($this->updatedBy),
            'deleted_by' => new UserResource($this->deleted_by),
        ];
    }
}
