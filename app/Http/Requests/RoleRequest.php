<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'nombre' => 'required|string|max:255|unique:roles,nombre' . ($isUpdate ? ',' . $this->route('role') : ''),
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permissions,id',
        ];
    }
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.max' => 'El nombre del rol no puede exceder los 255 caracteres.',
            'nombre.unique' => 'El nombre del rol ya está en uso.',
            'permisos.array' => 'Los permisos deben ser un arreglo.',
            'permisos.*.exists' => 'Uno o más permisos no existen.',
        ];
    }
}
