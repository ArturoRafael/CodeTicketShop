<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupoVendedoresPto;
use App\Models\GrupsVendedore;
use App\Models\PuntoVentum;
use Validator;
use Illuminate\Support\Facades\Input;

class GrupoVendedoresPtoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $grupo_vendors_pto = GrupoVendedoresPto::paginate(15);
        return $this->sendResponse($grupo_vendors_pto->toArray(), 'Grupo de Vendedores por punto de venta devueltos con éxito');
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
            'id_grupo_vendedores' => 'required', 
            'id_punto_venta' => 'required',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $grupo_vendors_pto_search = GrupoVendedoresPtoController::grupo_vendors_pto_search($request->input('id_grupo_vendedores'), $request->input('id_punto_venta'));
        
        
        if(count($grupo_vendors_pto_search) != 0){
           return $this->sendError('Grupo de Vendedores por punto de venta ya existe'); 
        }

        $grupo_vendors = GrupsVendedore::find($request->input('id_grupo_vendedores'));
        if (is_null($grupo_vendors)) {
            return $this->sendError('El grupo de vendedores indicado no existe');
        }

        $pto_venta = PuntoVentum::find($request->input('id_punto_venta'));
        if (is_null($pto_venta)) {
            return $this->sendError('El punto de venta indicado no existe');
        }

        $grupo_vendors_pto = GrupoVendedoresPto::create($request->all());        
        return $this->sendResponse($grupo_vendors_pto->toArray(), 'Grupo de Vendedores por punto de venta creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   

        $grupo_vendors_pto = GrupoVendedoresPto::where('id_grupo_vendedores','=',$id)->get();
        if (count($grupo_vendors_pto) == 0) {
            return $this->sendError('Puntos de venta por grupo de vendedor no encontrados');
        }
        return $this->sendResponse($grupo_vendors_pto->toArray(), 'Puntos de venta por grupo de vendedor devueltos con éxito');
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
            'id_punto_venta_old' => 'required',
            'id_punto_venta_new' => 'required',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $grupo_vendors_pto_search = GrupoVendedoresPtoController::grupo_vendors_pto_search($id, $input['id_punto_venta_old']);

        if(count($grupo_vendors_pto_search) != 0){

            $grupo_vendors = GrupsVendedore::find($id);
            if (is_null($grupo_vendors)) {
                return $this->sendError('El grupo de vendedores indicado no existe');
            }

            $pto_venta = PuntoVentum::find($input['id_punto_venta_new']);
            if (is_null($pto_venta)) {
                return $this->sendError('El punto de venta indicado no existe');
            }

            $grupo_vendors_pto_search2 = GrupoVendedoresPtoController::grupo_vendors_pto_search($id, $input['id_punto_venta_new']);
            
            if(count($grupo_vendors_pto_search2) != 0){
                return $this->sendError('Grupo de vendedor por punto de venta ya existe'); 
            }
            
        }else{
           return $this->sendError('Grupo de vendedor por punto de venta no se encuentra'); 
        }

        GrupoVendedoresPto::where('id_grupo_vendedores','=',$id)
                            ->where('id_punto_venta','=', $input['id_punto_venta_old'])
                            ->update(['id_punto_venta' => $input['id_punto_venta_new']]);  
        
        $grupo_vendors_pto = GrupoVendedoresPtoController::grupo_vendors_pto_search($id, $input['id_punto_venta_new']);
                            
        return $this->sendResponse($grupo_vendors_pto->toArray(), 'Grupo de vendedor por punto de venta actualizado con éxito');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {      

        $grupo_vendors_pto = GrupoVendedoresPto::where('id_grupo_vendedores','=',$id)->get();
        if (count($grupo_vendors_pto) == 0) {
            return $this->sendError('Puntos de venta por grupo de vendedor no encontrados');
        }
        GrupoVendedoresPto::where('id_grupo_vendedores','=',$id)->delete();
        return $this->sendResponse($grupo_vendors_pto->toArray(), 'Puntos de venta por grupo de vendedor eliminados con éxito');
       
    }


    public function grupo_vendors_pto_search($id_grupo_vendedores, $id_punto_venta){

        $search = GrupoVendedoresPto::where('id_grupo_vendedores','=',$id_grupo_vendedores)
                                     ->where('id_punto_venta','=', $id_punto_venta)->get();
        return $search;
    }
}
