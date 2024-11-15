<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'date' => $this->date ? Carbon::parse($this->date)->format('d-m-Y H:i:s') : null,
            'date_expiration' => $this->date_expiration ? Carbon::parse($this->date_expiration)->format('d-m-Y H:i:s') : null,
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => $this->updated_by ? new UserResource($this->updatedBy) : null,
            'deleted_by' => $this->deleted_by ? new UserResource($this->deletedBy) : null,
        ];
    }
}
