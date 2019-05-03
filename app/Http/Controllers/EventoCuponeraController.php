<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Cuponera;
use App\Models\EventoCuponera;
use Illuminate\Support\Facades\Input;
use Validator;

class EventoCuponeraController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evento_cuponera = EventoCuponera::paginate(15);
        return $this->sendResponse($evento_cuponera->toArray(), 'Cupones por evento devueltos con éxito');
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
            'id_evento' => 'required|integer', 
            'id_cuponera' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento_cuponera_search = EventoCuponeraController::evento_cuponera_search($request->input('id_evento'), $request->input('id_cuponera'));

        if(count($evento_cuponera_search) != 0){
           return $this->sendError('Cuponera por evento ya existe'); 
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $cuponera = Cuponera::find($request->input('id_cuponera'));
        if (is_null($cuponera)) {
            return $this->sendError('La cuponera indicada no existe');
        }

        $evento_cuponera = EventoCuponera::create($request->all());        
        return $this->sendResponse($evento_cuponera->toArray(), 'Cuponera por evento creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evento_cuponera = EventoCuponera::where('id_evento','=',$id)->get();
        if (count($evento_cuponera) == 0) {
            return $this->sendError('El evento no contiene cuponeras asociadas');
        }
        return $this->sendResponse($evento_cuponera->toArray(), 'Cuponeras por evento devueltas con éxito');
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
            'id_cuponera_old' => 'required|integer', 
            'id_cuponera_new' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $evento_cuponera_search = EventoCuponeraController::evento_cuponera_search($id, $input['id_cuponera_old']);
        if(count($evento_cuponera_search) != 0){

            $evento = Evento::find($id);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            $cuponera = Cuponera::find($input['id_cuponera_new']);
            if (is_null($cuponera)){
                return $this->sendError('La cuponera indicada no existe');
            }

            $evento_cuponera_search2 = EventoCuponeraController::evento_cuponera_search($id, $input['id_cuponera_new']);
            
            if(count($evento_cuponera_search2) != 0){
                return $this->sendError('Cuponera por evento ya existe'); 
            }

        }else{
             return $this->sendError('No se encuentran cuponeras por evento');
        }

        EventoCuponera::where('id_evento','=',$id)
                            ->where('id_cuponera','=', $input['id_cuponera_old'])
                            ->update(['id_cuponera' => $input['id_cuponera_new']]);  
        
        $evento_cuponera = EventoCuponeraController::evento_cuponera_search($id, $input['id_cuponera_new']);
                            
        return $this->sendResponse($evento_cuponera->toArray(), 'Cuponera por evento actualizada con éxito');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento_cuponera = EventoCuponera::where('id_evento','=',$id)->get();
        if (count($evento_cuponera) == 0) {
            return $this->sendError('El evento no contiene cuponeras asociadas');
        }
        EventoCuponera::where('id_evento','=',$id)->delete();
        return $this->sendResponse($evento_cuponera->toArray(), 'Cuponeras por evento eliminadas con éxito');
    }

    public function evento_cuponera_search($id_evento, $id_cuponera){

        $search = EventoCuponera::where('id_evento','=',$id_evento)
                                     ->where('id_cuponera','=', $id_cuponera)->get();
        return $search;
    }
}