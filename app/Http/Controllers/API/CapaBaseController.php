<?php

namespace Agencia\Http\Controllers\API;

use Agencia\CapaBase;
use Illuminate\Http\Request;
use Agencia\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class CapaBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $capasBases= CapaBase::all();

        $response =Response::json([
            'mensaje'=>'Capas Bases obtenidas correctamente',
            'capasBases'=>$capasBases],
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
        if (!trim($request->nombre) && !trim($request->url) && !trim($request->capa) ) {
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos obligatorios del formulario']
            ],
                422);
            return $response;
        }

        $capaBase=new CapaBase();

        $capaBase->nombre=trim($request->nombre);
        $capaBase->nombre=trim($request->url);
        $capaBase->nombre=trim($request->capa);
        $capaBase->nombre=trim($request->attribution);

        $capaBase->save();

        $response =Response::json([
            'mensaje'=>'La capa base ha sido añadida correctamente',
            'capaBase'=>$capaBase],
            201);

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
                'Error' => ['mensaje' => 'La capa base no existe']
            ], 404);
        }

        $capaBase=CapaBase::find($id);
        if(!$capaBase) {
            return Response::json([
                'Error' => ['mensaje' => 'No se encontró la capa base']
            ], 404);
        }

        $response = Response::json([
            'mensaje'=>'Capa base obtenida correctamente',
            'capaBase'=>$capaBase ],
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
        if (!trim($request->nombre) && !trim($request->url) && !trim($request->capa)) {
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos obligatorios del formulario']
            ],
                422);

            return $response;
        }

        $capaBase = CapaBase::find($id);
        if(!$capaBase){
            return Response::json([
                'Error' => ['mensaje'=> 'No se ha encontrado la capa base']
            ], 404);
        }

        $capaBase->nombre= trim($request->nombre);
        $capaBase->nombre= trim($request->url);
        $capaBase->nombre= trim($request->capa);
        $capaBase->nombre= trim($request->attribution);

        $capaBase->save();

        $response =Response::json([
            'mensaje'=>'La capa base ha sido actualizada correctamente',
            'capaBase' => $capaBase
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
        if(!$id || !is_numeric($id)){
            return Response::json([
                'Error' => ['mensaje' => 'La capa base no existe']
            ], 404);
        }

        $capaBase=CapaBase::find($id);
        if(!$capaBase) {
            return Response::json([
                'Error' => ['mensaje' => 'No se encontró la capa base']
            ], 404);
        }

        $capaBase->delete();

        $response =Response::json([
            'mensaje'=>'La capa base ha sido eliminada correctamente'],
            200);

        return $response;

    }
}
