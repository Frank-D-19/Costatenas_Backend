<?php

namespace Agencia\Http\Controllers\API;

use Agencia\Indicador;
use Agencia\Parametro;
use Agencia\Unidad_Medida;
use Illuminate\Http\Request;
use Agencia\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ParametroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parametros = Parametro::all();
        $pars=[];
        foreach ($parametros as $parametro) {
            $p=new Parametro();

            $p->id=$parametro->id;
            $p->nombre=$parametro->nombre;
            $p->indicador= $parametro->indicador;
            $p->unidad_medida=$parametro->unidadMedida;
            $p->zonas=$parametro->zonas;
            $p->created_at=$parametro->created_at;
            $p->updated_at=$parametro->updated_at;

            $pars[]=$p;
        }
        $response =Response::json([
            'mensaje'=>'Parametros obtenidos correctamente',
            'parametros'=>$pars],
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

        if ((!trim($request->nombre)) || (!trim($request->unidad_medida))){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos del formulario']
            ],
                422);
            return $response;
        }

       $parametro= Parametro::create([
            'nombre'=>$request['nombre'],
            'unidad_medida_id'=>$request['unidad_medida']
        ]);

      /*  $parametro->nombre=$request->nombre;
        $parametro->unidad_medida_id=$unidad_medida1->id;
        //$parametro->indicador_id=$request->indicador['id'];
        $parametro->save();*/

        $zonas=[];

        foreach ($request->zonas as $z) {
            $zonas[]=$z;
        }

        foreach ($zonas as $zona) {
            $parametro->zonas()->attach($zona, ['is_active' => true]);
        }

        $response =Response::json([
            'mensaje'=>'El parametro ha sido añadido correctamente',
            'parametro'=>$parametro],
            201);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Parametro $parametro)
    {
        if(!$parametro){
            return Response::json([
                'Error'=>['message'=> 'No se ha encontrado el parámetro']
            ], 404);
        }
        $unidad_medida=$parametro->unidadMedida;
        $indicador=$parametro->indicador;
        $response =Response::json([
            'mensaje'=>'El parametro ha sido mostrado correctamente',
            'parametro' => $parametro
        ],
            201);

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
        $parametro = Parametro::find($id);
        if(!$parametro){
            return Response::json([
                'Error'=>['message'=> 'No se ha encontrado el parámetro']
            ], 404);
        }

        $parametro->nombre= $request->nombre;
        $parametro->unidad_medida_id=$request->unidad_medida['id'];
        $parametro->indicador_id=$request->indicador['id'];

        $parametro->save();

        /*$p= new Parametro();
        $p->id=$parametro->id;
        $p->nombre=$parametro->nombre;
        $p->unidad_medida=$parametro->unidadMedida;
        $p->indicador=$parametro->indicador;
        $p->zonas=$parametro->zonas;
        $p->created_at=$parametro->created_at;
        $p->updated_at=$parametro->updated_at;*/

        $response =Response::json([
            'mensaje'=>'El parametro ha sido actualizado correctamente',
            'parametro' => $parametro
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
    public function destroy(Parametro $parametro)
    {
        if(!$parametro){
            return Response::json([
                'Error'=>['message'=> 'No se ha encontrado el parámetro']
            ], 404);
        }
        $parametro->delete();
        $response =Response::json([
            'mensaje'=>'El parametro ha sido eliminado correctamente'],
            200);

        return $response;
    }
}
