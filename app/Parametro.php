<?php

namespace Agencia;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $fillable=['nombre','unidad_medida_id'];

    public function indicador(){
        return $this->belongsTo('Agencia\Indicador');
    }

    public function unidadMedida()
    {
        return $this->belongsTo('Agencia\Unidad_Medida');
    }

    public function zonas(){
        return $this->belongsToMany('Agencia\Zona', 'parametro_zonas', 'parametro_id', 'zona_id')
            ->using('Agencia\Parametro_Zona')
            ->withPivot('is_active')
            ->withTimestamps();
    }
}
