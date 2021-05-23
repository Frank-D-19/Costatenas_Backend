<?php

namespace Agencia;

use Illuminate\Database\Eloquent\Model;

use Phaza\LaravelPostgis\Eloquent\PostgisTrait;

class Zona extends Model
{
    use PostgisTrait;

    protected $postgisFields = [
        'geom'
    ];
    protected $postgisTypes =[
        'geom'=>[
            'geomtype'=>'geometry',
            'srid'=> 27000
        ]
    ];

    public function parametros(){
        return $this->belongsToMany('Agencia\Parametro', 'parametro_zonas', 'zona_id', 'parametro_id')
            ->using('Agencia\Parametro_Zona')
            ->withPivot('is_active')
            ->withTimestamps();
    }

}
