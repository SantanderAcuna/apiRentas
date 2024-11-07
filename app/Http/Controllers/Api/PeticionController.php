<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PeticionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $indiceActual = 0;
    public function desembargo()
    {
        // Obtiene todos los registros de usuarios desde la base de datos
        $user = User::all();

        // Verifica si la colección de usuarios está vacía
        if ($user->isEmpty()) {
            // Si no hay usuarios, responde con un mensaje indicando el error
            return response()->json([
                "message" => "No hay funcionario en la base de datos",
                "status" => false,
            ]);
        }

        // Selecciona el usuario actual basándose en el índice `indiceActual`
        // `$this->indiceActual` apunta al funcionario que está siendo asignado actualmente
        $funcionario = $user[$this->indiceActual];

        // Prepara el mensaje de asignación
        // Indica a qué funcionario se le ha asignado la petición actual
        $asignacion = [
            "message" => "Peticion asignada al funcionario: {$funcionario->nombre}",
            "status" => true,
        ];

        // Actualiza `indiceActual` para apuntar al siguiente funcionario en la lista
        // Esto asegura que en la siguiente ejecución se asignará la petición al siguiente funcionario
        // El operador % permite que, al llegar al último funcionario, vuelva al inicio de la lista

        $this->indiceActual = ($this->indiceActual + 1) % $user->count();

        // Retorna la respuesta en formato JSON con la información del funcionario asignado
        return response()->json([
            "message" => "Peticion asignada Exitosamente a: {$funcionario->nombre}",
            "status" => true,
        ]);
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
