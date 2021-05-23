<?php

namespace Agencia\Http\Controllers\API;

use Agencia\Indicador;
use Agencia\Parametro;
use Agencia\Unidad_Medida;
use Illuminate\Http\Request;
use Agencia\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class IndicadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $indicadores= Indicador::all();
       $inds=[];
        foreach ($indicadores as $indicador) {
            $i=new Indicador();

            $i->id=$indicador->id;
            $i->nombre=$indicador->nombre;
            $i->definicion=$indicador->definicion;
            $i->importancia=$indicador->importancia;
            $i->colaboracion=$indicador->colaboracion;
            $i->fuente=$indicador->fuente;
            $i->color=$indicador->color;
            $i->valoracion=$indicador->valoracion;
            $i->procedimiento=$indicador->procedimiento;

            $pars=[];
            foreach ($indicador->parametros as $parametro) {
                $p= new Parametro();
                $p->id=$parametro->id;
                $p->nombre=$parametro->nombre;
                $p->indicador=$parametro->indicador;
                $p->unidad_medida=$parametro->unidadMedida;
                $p->zonas=$parametro->zonas;
                $pars[]=$p;
            }

            $i->parametros=$pars;
            $i->created_at=$indicador->created_at;
            $i->updated_at=$indicador->updated_at;

            $inds[]=$i;
       }

        $response = Response::json([
            'mensaje'=>'Indicadores obtenidos correctamente',
            'indicadores'=>$inds],
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
        if ((!trim($request->nombre)) || (!trim($request->definicion)) || (!trim($request->importancia)) || (!trim($request->colaboracion)) || (!trim($request->fuente))){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos del formulario']
            ],
                422);
            return $response;
        }
        $indicador=new Indicador();

        $indicador->nombre=$request->nombre;
        $indicador->definicion=$request->definicion;
        $indicador->importancia=$request->importancia;
        $indicador->colaboracion=$request->colaboracion;
        $indicador->fuente=$request->fuente;
        $indicador->color=$request->color;
        $indicador->valoracion=$request->valoracion;
        $indicador->procedimiento=$request->procedimiento;

        $indicador->save();

        $response =Response::json([
            'mensaje'=>'El indicador ha sido añadido correctamente',
            'indicador'=>$indicador],
            201);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Indicador $indicadore)
    {
        $i=new Indicador();

        $i->id=$indicadore->id;
        $i->nombre=$indicadore->nombre;
        $i->definicion=$indicadore->definicion;
        $i->importancia=$indicadore->importancia;
        $i->colaboracion=$indicadore->colaboracion;
        $i->fuente=$indicadore->fuente;
        $i->color=$indicadore->color;
        $i->valoracion=$indicadore->valoracion;
        $i->procedimiento=$indicadore->procedimiento;



        $pars=[];
        foreach ($indicadore->parametros as $parametro) {
            $p= new Parametro();

            $p->id=$parametro->id;
            $p->nombre=$parametro->nombre;
            $p->indicador=$parametro->indicador;
            $p->unidad_medida=$parametro->unidadMedida;
            $p->zonas=$parametro->zonas;

            $pars[]=$p;
        }

        $i->parametros=$pars;
        $i->created_at=$indicadore->created_at;
        $i->updated_at=$indicadore->updated_at;

        $response =Response::json([
            'mensaje'=>'El indicador ha sido obtenido correctamente',
            'indicador'=>$i],
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
        if ((!$request->nombre) || (!$request->definicion) || (!$request->importancia) || (!$request->fuente) || (!$request->colaboracion)){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos del formulario']
            ],
                422);
            return $response;
        }

        $indicador = Indicador::find($id);
        if(!$indicador){
            return Response::json([
                'Error'=>['mensaje'=> 'No se ha encontrado el indicador']
            ], 404);
        }

        $indicador->nombre= $request->nombre;
        $indicador->definicion= $request->definicion;
        $indicador->importancia= $request->importancia;
        $indicador->fuente= $request->fuente;
        $indicador->colaboracion= $request->colaboracion;
        $indicador->color=$request->color;
        $indicador->valoracion=$request->valoracion;
        $indicador->procedimiento=$request->procedimiento;

        $indicador->save();

        $response =Response::json([
            'mensaje'=>'El indicador ha sido actualizado correctamente',
            'indicador' => $indicador
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
    public function destroy(Indicador $indicadore)
    {
        $indicadore->delete();

        $response =Response::json([
            'mensaje'=>'El indicador ha sido eliminado correctamente'],
            200);

        return $response;
    }
}
