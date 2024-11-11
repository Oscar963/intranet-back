<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => 'required|string|max:255|unique:permissions,name' . ($isUpdate ? ',' . $this->route('permission') : ''),
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.max' => 'El nombre del permiso no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre del permiso ya está en uso.',
            'description.string' => 'La descripción debe ser un texto.',
        ];
    }
}
