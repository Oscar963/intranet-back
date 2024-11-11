<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Example;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExampleController extends Controller
{
    /**
     * Obtener todos los ejemplos
     * 
     * Endpoint: GET /api/examples
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener todos los registros del modelo Example
        $examples = Example::all();

        // Devolver respuesta en formato JSON
        return response()->json(['data' => $examples], 200);
    }

    /**
     * Crear un nuevo ejemplo
     * 
     * Endpoint: POST /api/examples
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:examples',
            'description' => 'nullable|string',
        ]);

        // Manejar errores de validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear un nuevo registro del modelo Example
        $example = new Example();
        $example->name = $request->name;
        $example->description = $request->description;
        $example->save();

        // Devolver respuesta de éxito en formato JSON
        return response()->json(['data' => $example], 201);
    }

    /**
     * Obtener un ejemplo específico
     * 
     * Endpoint: GET /api/examples/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Buscar el registro por ID
        $example = Example::find($id);

        // Manejar caso de registro no encontrado
        if (!$example) {
            return response()->json(['message' => 'Example not found'], 404);
        }

        // Devolver respuesta en formato JSON
        return response()->json(['data' => $example], 200);
    }

    /**
     * Actualizar un ejemplo
     * 
     * Endpoint: PUT/PATCH /api/examples/{id}
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Buscar el registro por ID
        $example = Example::find($id);

        // Manejar caso de registro no encontrado
        if (!$example) {
            return response()->json(['message' => 'Example not found'], 404);
        }

        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:examples,name,' . $id,
            'description' => 'nullable|string',
        ]);

        // Manejar errores de validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Actualizar el registro del modelo Example
        if ($request->has('name')) {
            $example->name = $request->name;
        }

        if ($request->has('description')) {
            $example->description = $request->description;
        }

        $example->save();

        // Devolver respuesta de éxito en formato JSON
        return response()->json(['data' => $example], 200);
    }

    /**
     * Eliminar un ejemplo
     * 
     * Endpoint: DELETE /api/examples/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Buscar el registro por ID
        $example = Example::find($id);

        // Manejar caso de registro no encontrado
        if (!$example) {
            return response()->json(['message' => 'Example not found'], 404);
        }

        // Eliminar el registro
        $example->delete();

        // Devolver respuesta de éxito en formato JSON
        return response()->json(['message' => 'Example deleted successfully'], 204);
    }
}
