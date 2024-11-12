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
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'tipo_peticion' => [
                'required',
                'string',
                'max:255',
                Rule::in(['Exoneracion', 'Desembargo', 'Prescripcion']) // Agrega todos los tipos válidos aquí
            ],
            'fecha_asignacion' => 'required|date',
            'contribuyente_id' => 'required|exists:contribuyentes,id', // Confirma que exista el contribuyente
            'funcionario_id' => 'required|exists:users,id', // Confirma que exista el funcionario
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_asignacion'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear la petición en la base de datos
        $peticion = Peticion::create([
            'tipo_peticion' => $request->input('tipo_peticion'),
            'fecha_asignacion' => $request->input('fecha_asignacion'),
            'contribuyente_id' => $request->input('contribuyente_id'),
            'funcionario_id' => $request->input('funcionario_id'),
            'fecha_vencimiento' => $request->input('fecha_vencimiento')
        ]);

        if (!$peticion) {
            return response()->json([
                'status' => false,
                'message' => 'Error al guardar la petición'
            ], 500);
        }

        // Aquí continúa el proceso de asignación si es necesario

        // Inicia el array de asignaciones y turnos
        $nuevasAsignaciones = [];
        //var_dump("Proceso iniciado", $nuevasAsignaciones); // Confirma el inicio del proceso

        // Función de búsqueda insensible a mayúsculas/minúsculas
        function searchAsignadoId($needle, $haystack)
        {
            if (!is_string($needle) || !is_array($haystack)) {
                //var_dump("searchAsignadoId: Parámetros inválidos", $needle, $haystack); // Verifica parámetros
                return false;
            }

            foreach ($haystack as $key => $value) {
                if (strcasecmp($value, $needle) === 0) {
                    //var_dump("searchAsignadoId: Coincidencia encontrada", $key, $value, $needle); // Muestra coincidencia
                    return $key;
                }
            }
            //var_dump("searchAsignadoId: Sin coincidencia", $needle); // Muestra cuando no encuentra coincidencia
            return false;
        }

        // Paso 1: Obtiene las asignaciones mapeando `id` a `nombre`
        $asignaciones = Asignacion::pluck('nombre', 'id')->toArray();
        //var_dump("Asignaciones obtenidas", $asignaciones); // Verifica las asignaciones obtenidas
        //dd($asignaciones, "Paso 1 completado - Asignaciones obtenidas");

        // Paso 2: Obtiene todas las peticiones sin asignar
        $peticionesSinAsignar = Peticion::whereNull('funcionario_id')->get(['id', 'tipo_peticion']);
        //var_dump("Peticiones sin asignar obtenidas", $peticionesSinAsignar); // Confirma que hay peticiones sin asignar
        //dd($peticionesSinAsignar, "Paso 2 completado - Peticiones sin asignar");

        // Paso 3: Agrupa las peticiones sin asignar por `tipo_peticion`
        $peticionesAgrupadas = $peticionesSinAsignar->groupBy('tipo_peticion');
        //var_dump("Peticiones agrupadas por tipo", $peticionesAgrupadas); // Verifica la estructura de agrupación
        //dd($peticionesAgrupadas, "Paso 3 completado - Peticiones agrupadas");

        // Paso 4: Obtiene los funcionarios agrupados por `asignado_id`
        $funcionarios = User::whereIn('asignado_id', array_keys($asignaciones))
            ->orderBy('id')
            ->get(['id', 'asignado_id'])
            ->groupBy('asignado_id');
        //var_dump("Funcionarios agrupados por asignado_id", $funcionarios); // Verifica que los funcionarios están correctamente agrupados
        //dd($funcionarios, "Paso 4 completado - Funcionarios agrupados");

        // Paso 5: Asigna cada grupo de peticiones según `tipo_peticion`
        foreach ($peticionesAgrupadas as $tipoPeticion => $peticiones) {
            $asignadoId = searchAsignadoId($tipoPeticion, $asignaciones);
            //var_dump("Asignado ID para tipo de petición", $tipoPeticion, $asignadoId); // Muestra el ID obtenido para el tipo de petición actual

            $funcionariosParaTipo = $funcionarios->get($asignadoId, collect());
            //var_dump("Funcionarios disponibles para el asignado ID", $asignadoId, $funcionariosParaTipo); // Verifica que haya funcionarios para el `asignadoId`
            //dd($asignadoId, $funcionariosParaTipo, "Paso 5 completado - Funcionario y asignadoId validados");

            if ($asignadoId !== false && $funcionariosParaTipo->isNotEmpty()) {
                // Obtiene el último turno desde la tabla `turnos_asignacion`
                $turno = DB::table('turnos_asignacion')
                    ->where('asignado_id', $asignadoId)
                    ->value('turno') ?? 0;
                //var_dump("Turno actual en turnos_asignacion", $asignadoId, $turno); // Verifica el turno actual
                //dd($turno, "Turno inicial para asignaciones");

                $funcionariosIds = $funcionariosParaTipo->pluck('id')->toArray();
                //var_dump("IDs de funcionarios para asignación cíclica", $funcionariosIds); // Muestra los IDs de funcionarios obtenidos
                //dd($funcionariosIds, "IDs de funcionarios obtenidos");

                // Asigna cada petición a un funcionario en orden cíclico
                foreach ($peticiones as $peticion) {
                    $funcionarioId = $funcionariosIds[$turno];
                    //var_dump("Asignando petición a funcionario", $peticion, $funcionarioId, $turno); // Verifica los datos de petición y funcionario seleccionados

                    Peticion::where('id', $peticion->id)->update(['funcionario_id' => $funcionarioId]);
                    //var_dump("Petición actualizada con funcionario", $peticion->id, $funcionarioId); // Confirma que la petición fue actualizada

                    // Guarda la asignación en el array de nuevas asignaciones
                    $nuevasAsignaciones[] = [
                        'peticion_id' => $peticion->id,
                        'tipo_peticion' => $tipoPeticion,
                        'funcionario_id' => $funcionarioId,
                    ];
                    //var_dump("Nueva asignación registrada", end($nuevasAsignaciones)); // Muestra la última asignación registrada

                    // Inserta el registro en el historial de asignaciones
                    DB::table('historial_asignaciones')->insert([
                        'asignado_id' => $asignadoId,
                        'funcionario_id' => $funcionarioId,
                        'peticion_id' => $peticion->id,
                        'fecha_asignacion' => now(),
                    ]);
                    //var_dump("Registro insertado en historial_asignaciones", $asignadoId, $funcionarioId, $peticion->id);

                    // Avanza el turno al siguiente funcionario y actualiza el índice del turno
                    $turno = ($turno + 1) % count($funcionariosIds);
                    //var_dump("Nuevo turno calculado", $turno); // Verifica el nuevo valor de turno
                    //dd($turno, "Nuevo turno calculado");
                }

                // Guarda el turno actualizado en la base de datos
                DB::table('turnos_asignacion')
                    ->updateOrInsert(
                        ['asignado_id' => $asignadoId],
                        ['turno' => $turno]
                    );
                var_dump("Turno actualizado en turnos_asignacion", $asignadoId, $turno); // Verifica que el turno se actualizó
                dd($turno, "Turno actualizado en la base de datos");
            }
        }

        // Retorna el panorama completo de las asignaciones
        return response()->json([
            "status" => true,
            "message" => "Petición guardada y asignada correctamente",
            "peticion" => [
                "id" => $peticion->id,
                "tipo_peticion" => $peticion->tipo_peticion,
                "contribuyente_id" => $peticion->contribuyente_id,
                "funcionario_id" => $peticion->funcionario_id,
                "fecha_asignacion" => $peticion->fecha_asignacion,
                "fecha_vencimiento" => $peticion->fecha_vencimiento
            ],
            "nuevasAsignaciones" => $nuevasAsignaciones,
            "detalle_asignaciones" => empty($nuevasAsignaciones)
                ? 'No hubo nuevas asignaciones'
                : 'Se asignaron ' . count($nuevasAsignaciones) . ' peticiones a funcionarios'
        ]);
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
