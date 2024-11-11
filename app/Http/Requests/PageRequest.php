<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $pageId = $isUpdate ? $this->route('page') : null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                // Validación de título único
                $isUpdate ? 'unique:pages,title,' . $pageId : 'unique:pages,title',
            ],
            'content' => 'nullable|string',
            'status' => 'required|in:published,hidden',
            'image_menu' => $isUpdate ? 'image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_banner' => $isUpdate ? 'image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'title.unique' => 'El título ya ha sido registrado, debe ser único.',
            'status.in' => 'El status debe ser publicado o oculto.',
            'image_menu.image' => 'El archivo debe ser una image.',
            'image_menu.mimes' => 'La image debe ser de tipo: jpeg, png, jpg, gif.',
            'image_menu.max' => 'La image no puede exceder los 2048 kilobytes.',
            'image_banner.image' => 'El archivo debe ser una image.',
            'image_banner.mimes' => 'La image debe ser de tipo: jpeg, png, jpg, gif.',
            'image_banner.max' => 'La image no puede exceder los 2048 kilobytes.',
        ];
    }
}
