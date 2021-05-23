<?php

namespace Agencia\Http\Controllers\API;

use Agencia\Medicion;
use Agencia\Parametro;
use Agencia\Zona;
use Illuminate\Http\Request;
use Agencia\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use \Phaza\LaravelPostgis\Geometries;

class ZonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zonas = Zona::all();
        $zs=[];
        foreach ($zonas as $zona) {
            $z = new Zona();

            $z->id = $zona->id;
            $z->nombre = $zona->nombre;
            $z->geom = $zona->geom;

            $pars=[];
            foreach ($zona->parametros as $parametro) {
                $p= new Parametro();

                $p->id=$parametro->id;
                $p->nombre=$parametro->nombre;
                $p->indicador=$parametro->indicador;
                $p->unidad_medida=$parametro->unidadMedida;
                $p->zonas=$parametro->zonas;
                $p->is_active=$parametro->pivot->is_active;

                $pz = DB::table('parametro_zonas')->where('parametro_id', $parametro->id)->where('zona_id', $zona->id)->first();

                $medicion= Medicion::where('parametro_zona_id',$pz->id)->latest('fecha')->first();
                //$ultimaMedicion=new Medicion();

                /*$ultimaMedicion->id= $medicion['id'];
                $ultimaMedicion->valor=$medicion['valor'];
                $ultimaMedicion->fecha=$medicion['fecha'];
                $ultimaMedicion->created_at=$medicion['created_at'];
                $ultimaMedicion->updated_at=$medicion['updated_at'];*/

                $p->ultimaMedicion= $medicion;

                $pars[]=$p;
            }
            $z->parametros = $pars;

            $zs[]=$z;
        }

        $response =Response::json([
            'mensaje'=>'Zonas obtenidas correctamente',
            'zonas'=>$zs],
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
        if ((!trim($request->nombre)) || (!$request->geom) || (!$request->parametros)){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos del formulario']
            ],
                422);
            return $response;
        }

        $zona= new Zona();
        $geometry=null;

        if ($request->geom['features'][0]['geometry']['type']=='Polygon')
        {
            $puntos= [];
            foreach ($request->geom['features'][0]['geometry']['coordinates'][0] as $coordenada) {
                $lat = $coordenada[0];
                $lon = $coordenada[1];
                $puntos[]=new Geometries\Point($lon,$lat);
            }
            $lineString= new Geometries\LineString($puntos);
            $geometry= new Geometries\Polygon([$lineString]);
        }
        else if($request->geom['features'][0]['geometry']['type']=='Point')
        {
                $lat = $request->geom['features'][0]['geometry']['coordinates'][0];
                $lon = $request->geom['features'][0]['geometry']['coordinates'][1];

            $geometry= new Geometries\Point($lon,$lat);
        }

        $zona->nombre=$request->nombre;
        $zona->geom=$geometry;
        $zona->save();

        $parametros=[];

        foreach ($request->parametros as $p) {
            $parametros[]=$p;
        }

        foreach ($parametros as $parametro) {
            $zona->parametros()->syncWithoutDetaching([$parametro => ['is_active' => true]]);
        }

        $response =Response::json([
            'mensaje'=>'La zona ha sido añadida correctamente',
            'zona'=>$parametros],
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
                'Error' => ['mensaje' => 'La zona no existe']
            ], 404);
        }

        $zona=Zona::find($id);
        if(!$zona) {
            return Response::json([
                'Error' => ['mensaje' => 'No se encontró la zona']
            ], 404);
        }

        $z= new Zona();

        $z->id=$zona->id;
        $z->nombre=$zona->nombre;
        $z->geom=$zona->geom;
        $z->created_at=$zona->created_at;
        $z->updated_at=$zona->updated_at;

        $parametros=[];

        foreach ($zona->parametros as $parametro) {
            $p = new Parametro();

            $p->id = $parametro->id;
            $p->nombre = $parametro->nombre;
            $p->indicador = $parametro->indicador;
            $p->unidad_medida = $parametro->unidadMedida;
            $p->zonas = $parametro->zonas;
            $p->is_active = $parametro->pivot->is_active;
            $parametros[]=$p;
        }

        $z->parametros=$parametros;

        $response = Response::json([
            'mensaje'=>'Zona obtenida correctamente',
            'zona'=>$z ],
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
        if ((!trim($request->nombre)) || (!$request->geom) || (!$request->parametros)){
            $response=Response::json([
                'Error' => ['mensaje'=>'Por favor llene todos los datos del formulario']
            ],
                422);
            return $response;
        }
        $zona=Zona::find($id);
        if(!$zona) {
            return Response::json([
                'Error' => ['mensaje' => 'No se encontró la zona']
            ], 404);
        }

        $zona->nombre=trim($request->nombre);

        if ($request->geom['features'][0]['geometry']['type']=='Polygon')
        {
            $puntos= [];
            foreach ($request->geom['features'][0]['geometry']['coordinates'][0] as $coordenada) {
                $lat = $coordenada[0];
                $lon = $coordenada[1];
                $puntos[]=new Geometries\Point($lon,$lat);
            }
            $lineString= new Geometries\LineString($puntos);
            $geometry= new Geometries\Polygon([$lineString]);
        }
        else if($request->geom['features'][0]['geometry']['type']=='Point')
        {
            $lat = $request->geom['features'][0]['geometry']['coordinates'][0];
            $lon = $request->geom['features'][0]['geometry']['coordinates'][1];

            $geometry= new Geometries\Point($lon,$lat);
        }

        $zona->geom=$geometry;
        $zona->save();

        $parametros_id_viejos=[];

        foreach ($zona->parametros as $parametro) {
            $parametros_id_viejos[] = $parametro->id;
        }

        $id_parametros_inactivos=array_diff($parametros_id_viejos,$request->parametros);

        foreach ($id_parametros_inactivos as $inactivo){
            $pz = DB::table('parametro_zonas')->where('parametro_id', $inactivo)->where('zona_id', $zona->id)->first();

            $medicion= Medicion::where('parametro_zona_id',$pz->id)->latest('fecha')->first();

            if(!$medicion){
                $zona->parametros()->detach();
            }
            else{
                $zona->parametros()->updateExistingPivot($inactivo,['is_active'=>false]);
            }
        }

        foreach ($request->parametros as $np_id) {
            $pz = DB::table('parametro_zonas')->where('parametro_id', $np_id)->where('zona_id', $zona->id)->first();

            if($pz){
                if($pz->is_active==false){
                    $zona->parametros()->updateExistingPivot($np_id,['is_active'=>true]);
                }
            }
            else{
                $zona->parametros()->syncWithoutDetaching([$np_id => ['is_active' => true]]);
            }
        }

        $response =Response::json([
            'mensaje'=>'La zona ha sido actualizada correctamente',
            'zona' => $zona],
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
                'Error' => ['mensaje' => 'La zona no existe']
            ], 404);
        }

        $zona=Zona::find($id);
        if(!$zona) {
            return Response::json([
                'Error' => ['mensaje' => 'No se encontró la zona']
            ], 404);
        }

        $zona->parametros()->detach();
        $zona->delete();

        $response =Response::json([
            'mensaje'=>'La zona ha sido eliminada correctamente'],
            200);

        return $response;
    }
}
