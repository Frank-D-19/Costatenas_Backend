<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->get('/access', function (Request $request) {
    return response()->json(["nombre"=>"hola"]);
});
Route::apiResource('user', 'API\UserController');
Route::apiResource('indicadores', 'API\IndicadorController');
Route::apiResource('unidades_medida', 'API\UnidadMedidaController');
Route::apiResource('zonas', 'API\ZonaController');
Route::apiResource('parametros', 'API\ParametroController');
Route::apiResource('mediciones', 'API\MedicionController');
Route::get('mediciones/zona/{zonaId}/parametro/{parametroId}', 'API\MedicionController@medicionesXparametroXzona');
Route::apiResource('capasBase', 'API\CapaBaseController');
Route::apiResource('tipoDocumento', 'API\TipoDocumentoCotroller');
Route::apiResource('documentos', 'API\DocumentoController');

