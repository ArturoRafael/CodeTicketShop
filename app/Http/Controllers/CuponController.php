<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cupon;
use App\Models\TipoCupon;
use App\Models\Cuponera;
use Validator;
use Illuminate\Support\Facades\Input;

class CuponController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cupon = Cupon::paginate(15);
        return $this->sendResponse($cupon->toArray(), 'Cupones devueltos con éxito');
    }

  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'id_tipo_cupon' => 'required',
            'id_cuponera' => 'required',      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $tipo_cupon = TipoCupon::find($request->input('id_tipo_cupon'));
        if (is_null($tipo_cupon)) {
            return $this->sendError('El tipo de cupon indicado no existe');
        }

        $cuponera = Cuponera::find($request->input('id_cuponera'));
        if (is_null($cuponera)) {
            return $this->sendError('La cuponera indicada no existe');
        }

        if(!is_null($request->input('monto'))){
            $validator = Validator::make($request->all(), [
                'monto' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }else{
            Input::merge(['monto' => 0]);
        }

        if(!is_null($request->input('porcentaje_descuento'))){
            $validator = Validator::make($request->all(), [
                'porcentaje_descuento' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }else{
            Input::merge(['porcentaje_descuento' => 0]);
        }

        $cupon = Cupon::create($request->all());        
        return $this->sendResponse($cupon->toArray(), 'Cupon creado con éxito');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cupon = Cupon::find($id);

        if (is_null($cupon)) {
            return $this->sendError('Cupon no encontrado');
        }
        return $this->sendResponse($cupon->toArray(), 'Cupon devuelto con éxito');
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
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required',
            'id_tipo_cupon' => 'required',
            'id_cuponera' => 'required',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $cupon_search = Cupon::find($id);
        if (is_null($cupon_search)) {
            return $this->sendError('Cupon no encontrado');
        } 


        $tipo_cupon = TipoCupon::find($input['id_tipo_cupon']);
        if (is_null($tipo_cupon)) {
            return $this->sendError('El tipo de cupon indicado no existe');
        }

        $cuponera = Cuponera::find($input['id_cuponera']);
        if (is_null($cuponera)) {
            return $this->sendError('La cuponera indicada no existe');
        }

        if(!is_null($input['monto'])){
            $validator = Validator::make($request->all(), [
                'monto' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $cupon_search->monto = $input['monto'];
        }else{
            $cupon_search->monto = 0;
        }

        if(!is_null($input['porcentaje_descuento'])){
            $validator = Validator::make($request->all(), [
                'porcentaje_descuento' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $cupon_search->monto = $input['porcentaje_descuento'];
        }else{
            $cupon_search->porcentaje_descuento = 0;
        }

        $cupon_search->codigo = $input['codigo'];
        $cupon_search->status = $input['status'];        
        $cupon_search->id_tipo_cupon = $input['id_tipo_cupon'];        
        $cupon_search->id_cuponera = $input['id_cuponera'];
        $cupon_search->cantidad_compra = $input['cantidad_compra'];        
        $cupon_search->cantidad_paga = $input['cantidad_paga'];
        $cupon_search->save();
        return $this->sendResponse($cupon_search->toArray(), 'Cupon actualizado con éxito');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       try { 

            $cupon = Cupon::find($id);
            if (is_null($cupon)) {
                return $this->sendError('Cupon no encontrado');
            }
            $cupon->delete();
            return $this->sendResponse($cupon->toArray(), 'Cupon eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Cupon no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
