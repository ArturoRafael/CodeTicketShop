<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use App\Models\Auditorio;
use App\Models\ImagenesAuditorio;
use Validator;

class ImagenesAuditorioController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $img_auditorio = ImagenesAuditorio::paginate(15);
        return $this->sendResponse($img_auditorio->toArray(), 'Imagenes de auditorios devueltas con éxito');
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
            'id_imagen' => 'required|integer',
            'id_auditorio' => 'required|integer',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $img_evento_search = ImagenesAuditorioController::img_auditorio_search($request->input('id_auditorio'), $request->input('id_imagen'));

        if(count($img_evento_search) != 0){
           return $this->sendError('El auditorio ya posee esa imagen asociada'); 
        }

        $auditorio = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio)) {
            return $this->sendError('El auditorio indicado no existe');
        }

        $imagen = Imagen::find($request->input('id_imagen'));
        if (is_null($imagen)) {
            return $this->sendError('La imagen indicada no existe');
        }
        
        $img_auditorio = ImagenesAuditorio::create($request->all());        
        return $this->sendResponse($img_auditorio->toArray(), 'Imagenes de auditorio creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $img_auditorio = ImagenesAuditorio::where('id_auditorio','=',$id)->get();
        if (count($img_auditorio) == 0) {
            return $this->sendError('Imagenes por auditorio no encontradas');
        }
        return $this->sendResponse($img_auditorio->toArray(), 'Imagenes por auditorio devueltas con éxito');
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
            'id_imagen_old' => 'required|integer',
            'id_imagen_new' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());      
        }
        $img_auditorio_search = ImagenesAuditorioController::img_auditorio_search($id, $input['id_imagen_old']);

        if(count($img_auditorio_search) != 0){

            $auditorio = Auditorio::find($id);
            if (is_null($auditorio)) {
                return $this->sendError('El auditorio indicado no existe');
            }

            $imagen = Imagen::find($input['id_imagen_new']);
            if (is_null($imagen)) {
                return $this->sendError('La imagen indicada no existe');
            }

            $img_auditorio_search2 = ImagenesAuditorioController::img_auditorio_search($id, $input['id_imagen_new']);
            
            if(count($img_auditorio_search2) != 0){
                return $this->sendError('La imagen por auditorio ya se encuentra asociada'); 
            }
            
        }else{
           return $this->sendError('La imagen por auditorio no se encuentra'); 
        }

        ImagenesAuditorio::where('id_auditorio','=',$id)
                            ->where('id_imagen','=', $input['id_imagen_old'])
                            ->update(['id_imagen' => $input['id_imagen_new']]);  
        
        $imagen_auditorio = ImagenesAuditorioController::img_auditorio_search($id, $input['id_imagen_new']);
                            
        return $this->sendResponse($imagen_auditorio->toArray(), 'Imagen por auditorio actualizada con éxito');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $imagen_auditorio = ImagenesAuditorio::where('id_auditorio','=',$id)->get();
        if (count($imagen_auditorio) == 0) {
            return $this->sendError('Imagenes por auditorio no encontradas');
        }
        ImagenesAuditorio::where('id_auditorio','=',$id)->delete();
        return $this->sendResponse($imagen_auditorio->toArray(), 'Imagenes por auditorio eliminadas con éxito');
    }

    public function img_auditorio_search($id_auditorio, $id_imagen){

        $search = ImagenesAuditorio::where('id_imagen','=',$id_imagen)
                                ->where('id_auditorio','=', $id_auditorio)->get();
        return $search;
    }
}