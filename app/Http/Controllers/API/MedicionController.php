<?php

namespace Agencia\Http\Controllers\API;

use Agencia\Medicion;
use Agencia\Parametro;
use Agencia\Parametro_Zona;
use Agencia\Zona;
use Illuminate\Http\Request;
use Agencia\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class MedicionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mediciones=Medicion::all();

        if(!$mediciones ) {
            return Response::json([
                'Error' => ['message' => 'No se encontraron las mediciones']
            ], 404);
        }

        $meds=[];

        foreach ($mediciones as $medicion) {
            $m= new Medicion();

            $m->valor=$medicion->valor;
            $m->fecha=$medicion->fecha;

            $pz=DB::table('parametro_zonas')->where('id',$medicion->parametro_zona_id)->get();
            $parametro = Parametro::find($pz[0]->parametro_id);
            $z= Zona::find($pz[0]->zona_id);

            $p= new Parametro();

            $p->id=$parametro->id;
            $p->nombre=$parametro->nombre;
            $p->indicador=$parametro->indicador;
            $p->unidad_medida=$parametro->unidadMedida;

            $m->parametro= $p;
            $m->zona= $z;
            $meds[]=$m;
        }

        $response = Response::json([
            'mensaje'=>'Mediciones obtenidas correctamente',
            'mediciones'=>$meds],
            200);

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function medicionesXparametroXzona($zonaId, $parametroId){
        if(!$zonaId || !$parametroId || !is_numeric($zonaId) || !is_numeric($parametroId)){
            return Response::json([
                'Error' => ['mensaje' => 'La zona y el parámetro no existen']
            ], 404);
        }

        $parametro= Parametro::find($parametroId);
        if(!$parametro) {
            return Response::json([
                'Error' => ['mensaje' => 'El parámetro al que pertenece las mediciones no se pudo encontrar']
            ], 404);
        }

        $zona= Zona::find($zonaId);
        if(!$zona) {
            return Response::json([
                'Error' => ['mensaje' => 'La zona a la que pertenece las mediciones no se pudo encontrar']
            ], 404);
        }

        $pz = DB::table('parametro_zonas')->where('parametro_id', $parametroId)->where('zona_id', $zonaId)->first();
        if(!$pz) {
            return Response::json([
                'Error' => ['mensaje' => 'No se pudo encontrar el parámetro ni la zona a la que pertenece las mediciones']
            ], 404);
        }


        $p=new Parametro();

        $p->id=$parametro->id;
        $p->nombre=$parametro->nombre;
        $p->indicador= $parametro->indicador;
        $p->unidad_medida=$parametro->unidadMedida;
        $p->created_at=$parametro->created_at;
        $p->updated_at=$parametro->updated_at;

        $mediciones= Medicion::where('parametro_zona_id',$pz->id)->orderBy('fecha', 'desc')->get();
        if(!$mediciones) {
            return Response::json([
                'Error' => ['mensaje' => 'No se pudo encontrar las mediciones']
            ], 404);
        }

        $meds=[];
        foreach ($mediciones as $medicion) {
            $m=new Medicion();

            $m->id=$medicion->id;
            $m->valor=$medicion->valor;
            $m->fecha=$medicion->fecha;
            $m->created_at=$medicion->created_at;
            $m->updated_at=$medicion->updated_at;
            $m->parametro=$parametro;
            $m->zona=$zona;

            $meds[]=$m;
        }

        $response = Response::json([
            'mensaje'=>'Mediciones obtenidas correctamente',
            'mediciones'=>$meds],
            200);

        return $response;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $medicion = new Medicion();

        $pz = DB::table('parametro_zonas')->where('parametro_id', trim($request->parametro))->where('zona_id', trim($request->zona))->first();
        if(!$pz) {
            return Response::json([
                'Error' => ['message' => 'No se pudo encontrar el parámetro ni la zona a la que pertenece la nueva medición']
            ], 404);
        }

        if (!trim($request->valor) || !trim($request->fecha)){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los campos del formulario']
            ],
                422);
            return $response;
        }

        $medicionesRepetidas=DB::table('medicions')->where('fecha',trim($request->fecha))->get();
        if($medicionesRepetidas->count()>=1){
            if($medicionesRepetidas->count()==1){
                if ($medicionesRepetidas[0]->id != $medicion->id){
                    $response=Response::json([
                        'mensaje'=>'Ya se realizó una medicion en esa fecha y hora'
                    ],
                        422);
                    return $response;
                }
            }else{
                $response=Response::json([
                    'mensaje'=>'Ya se realizó una medicion en esa fecha y hora'
                ],
                    422);
                return $response;
            }
        }

        $medicion->valor=trim($request->valor);
        $medicion->fecha=trim($request->fecha);
        $medicion->parametro_zona_id=trim($pz->id);

        $medicion->save();

        $response = Response::json([
            'mensaje'=>'La medición ha sido añadida correctamente',
            'medicion'=>$medicion],
            200);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!$id || !is_numeric($id)){
            return Response::json([
                'Error' => ['mensaje' => 'La medición no existe']
            ], 404);
        }

        $medicion=Medicion::find($id);
        if(!$medicion) {
            return Response::json([
                'Error' => ['mensaje' => 'No se encontró la medición']
            ], 404);
        }

        $m= new Medicion();

        $m->id=$medicion->id;
        $m->valor=$medicion->valor;
        $m->fecha=$medicion->fecha;
        $m->created_at=$medicion->created_at;
        $m->updated_at=$medicion->updated_at;

        $pz=DB::table('parametro_zonas')->find($medicion->parametro_zona_id);
        if(!$pz) {
            return Response::json([
                'Error' => ['mensaje' => 'No se pudo encontrar el parámetro ni la zona a la que pertenece la medición']
            ], 404);
        }


        $z=Zona::find($pz->zona_id);
        if(!$z) {
            return Response::json([
                'Error' => ['mensaje' => 'La zona a la que pertenece la medición no existe']
            ], 404);
        }
        $parametro=Parametro::find($pz->parametro_id);
        if(!$parametro) {
            return Response::json([
                'Error' => ['mensaje' => 'El parámetro al que pertenece la medición no existe']
            ], 404);
        }
        $p= new Parametro();

        $p->id=$parametro->id;
        $p->nombre=$parametro->nombre;
        $p->indicador= $parametro->indicador;
        $p->unidad_medida=$parametro->unidadMedida;
        $p->created_at=$parametro->created_at;
        $p->updated_at=$parametro->updated_at;

        $m->parametro=$p;
        $m->zona=$z;

        $response = Response::json([
            'mensaje'=>'Medición obtenida correctamente',
            'medicion'=>$m ],
            200);

        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$id || !is_numeric($id)){
            return Response::json([
                'Error' => ['mensaje' => 'La medición no existe']
            ], 404);
        }

        if (!trim($request->valor) || !trim($request->fecha)){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los campos del formulario']
            ],
                422);
            return $response;
        }

        $medicion = Medicion::find($id);
        if(!$medicion){
            return Response::json([
                'Error' => ['mensaje'=> 'No se pudo encontrar la medición']
            ], 404);
        }

        $medicionesRepetidas=DB::table('medicions')->where('fecha',trim($request->fecha))->get();
        if($medicionesRepetidas->count()>=1){
            if($medicionesRepetidas->count()==1){
                if ($medicionesRepetidas[0]->id != $medicion->id){
                    $response=Response::json([
                        'Error' => ['mensaje'=>'Ya se realizó una medicion en esa fecha y hora']
                    ],
                        422);
                    return $response;
                }
            }else{
                $response=Response::json([
                    'Error' => ['mensaje'=>'Ya se realizó una medicion en esa fecha y hora']
                ],
                    422);
                return $response;
            }
        }

        $pz=DB::table('parametro_zonas')
            ->where('parametro_id', trim($request->parametro))
            ->where('zona_id',trim($request->zona))
            ->first();
        if(!$pz) {
            return Response::json([
                'Error' => ['mensaje' => 'No se pudo encontrar el parámetro ni la zona a la que pertenece la nueva medición']
            ], 404);
        }

        $medicion->valor= trim($request->valor);
        $medicion->fecha= trim($request->fecha);
        $medicion->parametro_zona_id= $pz->id;
        $medicion->save();

        $m = new Medicion();
        $m->id=$medicion->id;
        $m->valor= $medicion->valor;
        $m->fecha= $medicion->fecha;
        $m->parametro= trim($request->parametro);
        $m->zona= trim($request->zona);

        $response =Response::json([
            'mensaje'=>'La medición ha sido actualizada correctamente',
            'medicion' => $m
        ],
            201);

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
