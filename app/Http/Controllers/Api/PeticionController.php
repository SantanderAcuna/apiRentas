<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Contribuyente;
use App\Models\Funcionario;
use App\Models\Peticion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PeticionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Inicializamos `indiceActual` como una propiedad de la clase para mantener
    // el índice del funcionario actual en cada ejecución del método
    private $indiceActual = 0;

    // Método para asignar una nueva petición a un funcionario



    public function index()
    {
        $peticiones = Peticion::all();

        return response()->json([
            "status" => true, // Estado de éxito de la asignación
            "message" => "Peticiónes listadas correctamente",
            "data" => $peticiones,
        ]);
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
        // Validación
        $validatedData = $request->validate([
            'tipo_peticion' => 'required|string|max:255',
            'contribuyente_id' => 'required|integer|exists:contribuyentes,id',
        ]);

        // Crear nueva petición
        $peticion = Peticion::create([
            'tipo_peticion' => $validatedData['tipo_peticion'],
            'fecha_asignacion' => now(),
            'contribuyente_id' => $validatedData['contribuyente_id'],
            'funcionario_id' => null,
            'fecha_vencimiento' => now()->addDays(15),
        ]);

        // Procesar asignaciones
        $nuevasAsignaciones = $this->procesarAsignaciones($peticion);

        // Retornar resultado
        return response()->json([
            'status' => true,
            'message' => 'Peticiones asignadas con éxito',
            'asignaciones' => $nuevasAsignaciones,
        ]);
    }

    private function procesarAsignaciones($nuevaPeticion)
    {
        // Obtener asignaciones y funcionarios disponibles
        $asignaciones = Asignacion::pluck('nombre', 'id')->toArray();
        $peticionesSinAsignar = Peticion::whereNull('funcionario_id')->get(['id', 'tipo_peticion']);
        $funcionarios = Funcionario::whereIn('asignado_id', array_keys($asignaciones))
            ->orderBy('id')
            ->get(['id', 'asignado_id'])
            ->groupBy('asignado_id');

        $nuevasAsignaciones = [];

        // Ejecutar la lógica de asignación en una transacción
        DB::transaction(function () use ($asignaciones, $peticionesSinAsignar, $funcionarios, &$nuevasAsignaciones) {
            $peticionesAgrupadas = $peticionesSinAsignar->groupBy('tipo_peticion');

            foreach ($peticionesAgrupadas as $tipoPeticion => $peticiones) {
                $asignadoId = $this->buscarAsignadoId($tipoPeticion, $asignaciones);

                if ($asignadoId !== false && $funcionariosParaTipo = $funcionarios->get($asignadoId)) {
                    $turno = DB::table('turnos_asignacion')->where('asignado_id', $asignadoId)->value('turno') ?? 0;
                    $funcionariosIds = $funcionariosParaTipo->pluck('id')->toArray();

                    $historialBatch = [];
                    foreach ($peticiones as $peticion) {
                        $funcionarioId = $funcionariosIds[$turno];
                        $peticion->update(['funcionario_id' => $funcionarioId]);

                        $nuevasAsignaciones[] = [
                            'peticion_id' => $peticion->id,
                            'tipo_peticion' => $tipoPeticion,
                            'funcionario_id' => $funcionarioId,
                        ];

                        $historialBatch[] = [
                            'asignado_id' => $asignadoId,
                            'funcionario_id' => $funcionarioId,
                            'peticion_id' => $peticion->id,
                            'fecha_asignacion' => now(),
                        ];

                        $turno = ($turno + 1) % count($funcionariosIds);
                    }

                    DB::table('historial_asignaciones')->insert($historialBatch);
                    DB::table('turnos_asignacion')
                        ->updateOrInsert(['asignado_id' => $asignadoId], ['turno' => $turno]);
                }
            }
        });

        return $nuevasAsignaciones;
    }

    private function buscarAsignadoId($tipoPeticion, $asignaciones)
    {
        foreach ($asignaciones as $id => $nombre) {
            if (strcasecmp($nombre, $tipoPeticion) === 0) {
                return $id;
            }
        }
        return false;
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
