<?php

namespace Agencia\Http\Controllers\API;

use Agencia\Parametro;
use Agencia\Unidad_Medida;
use Illuminate\Http\Request;
use Agencia\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class UnidadMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unidadMedidas= Unidad_Medida::all();
        $ums=[];
        foreach ($unidadMedidas as $unidadMedida) {
            $um=new Unidad_Medida();
            $um->id=$unidadMedida->id;
            $um->nombre=$unidadMedida->nombre;
            $pars=[];
            foreach ($unidadMedida->parametros as $parametro) {
                $p= new Parametro();

                $p->id=$parametro->id;
                $p->nombre=$parametro->nombre;
                $p->indicador=$parametro->indicador;
                $p->unidad_medida=$parametro->unidadMedida;

                $pars[]=$p;
            }
            $um->parametros=$pars;
//
            $ums[]=$um;
        }

        $response =Response::json([
            'mensaje'=>'Unidades de medida obtenidas correctamente',
            'unidades_medida'=>$ums],
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unidadMedida=Unidad_Medida::find($id);
        if(!$unidadMedida){
            return Response::json([
                'Error'=>['message'=> 'No se ha encontrado la unidad de medida']
            ], 404);
        }
        $response =Response::json([
            'mensaje'=>'La unidad de medida ha sido mostrada correctamente',
            'unidad_medida' => $unidadMedida
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
        //
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
