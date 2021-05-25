<?php

namespace Agencia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndicador extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //Validaciones
        return [
            'nombre' => 'required|min:5',
            'definicion' => 'required|min:5',
            'importancia' => 'required|min:5',
            'fuente' => 'required|min:5',
            'colaboracion' => 'required|min:5',
            'valoracion' => 'required|min:5',
            'procedimiento' => 'required|min:5',
            'parametros' => 'required',
            'color' => 'required'
        ];
    }
}
