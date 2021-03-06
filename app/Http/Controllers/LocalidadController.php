<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\Tribuna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
/**
 * @group Administración de Localidad
 *
 * APIs para la gestion de la tabla localidad
 */
class LocalidadController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla localidad paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localidad = Localidad::with('tribuna')->with('filas')->with('palcos')->with('puestos')->paginate(15);
        return $this->sendResponse($localidad->toArray(), 'Localidades devueltas con éxito');
    }


    /**
     * Lista de todas las localidades.
     *
     * @return \Illuminate\Http\Response
     */
    public function localidad_all()
    {
        $localidad = Localidad::with('tribuna')->with('filas')->with('palcos')->with('puestos')->get();

        return $this->sendResponse($localidad->toArray(), 'Localidades devueltas con éxito');
    }
   

    /**
     * Buscar localidades por descripción.
     *@bodyParam nombre string Nombre de la localidad.
     *@response{
     *    "nombre" : "Localidad",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarLocalidad(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $localidades = \DB::table('localidad')
                ->join('tribuna','tribuna.id','=','localidad.id_tribuna')
                ->where('localidad.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('localidad.*', 'tribuna.*')
                ->get();
            return $this->sendResponse($localidades->toArray(), 'Todas las localidades filtradas');
       }else{
            
            $localidades = \DB::table('localidad') 
                ->join('tribuna','tribuna.id','=','localidad.id_tribuna')               
                ->select('localidad.*', 'tribuna.*')
                ->get();
            return $this->sendResponse($localidades->toArray(), 'Todas las localidades devueltas'); 
       }

        
    }

    /**
     * Agrega un nuevo elemento a la tabla localidad
     *@bodyParam nombre string required Nombre de la localidad.
     *@bodyParam id_tribuna int required Id de la tribuna.
     *@bodyParam puerta_acceso string Puerta de acceso de la loccalidad. Defaults to 0
     *@bodyParam ruta string Ruta de la localidad.
     *@bodyParam url_imagen string Url de la imagen.
     * @response {
     *  "nombre": "Localidad New",
     *  "id_tribuna": 1, 
     *  "puerta_acceso":null,
     *  "ruta":null,
     *  "url_imagen": null     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',            
            'id_tribuna' => 'required',
            'puerta_acceso' => 'alpha_num|max:20',
            'ruta' => 'nullable|string',
            'url_imagen' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $tribuna = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna)) {
            return $this->sendError('La Tribuna indicada no existe');
        }

        $localidad = Localidad::create($request->all());        
        return $this->sendResponse($localidad->toArray(), 'Localidad creada con éxito');
    }

    /**
     * Lista de una localidad en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $localidad = Localidad::with('filas')->with('palcos')->with('puestos')->find($id);


        if (is_null($localidad)) {
            return $this->sendError('Localidad no encontrado');
        }


        return $this->sendResponse($localidad->toArray(), 'Localidad devuelta con éxito');
    }

     /**
     * Actualiza un elemeto de la tabla localidad 
     *@bodyParam nombre string required Nombre de la localidad.
     *@bodyParam id_tribuna int required Id de la tribuna.
     *@bodyParam puerta_acceso string Puerta de acceso de la loccalidad. Defaults to 0
     *@bodyParam ruta string Ruta de la localidad.
     *@bodyParam url_imagen string Url de la imagen.
     * [Se filtra por el ID]
     * @response {
     *  "nombre": "Localidad 2",
     *  "id_tribuna": 1, 
     *  "puerta_acceso":"AA12" 
     *  "ruta":null,
     *  "url_imagen":null      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',            
            'id_tribuna' => 'required',
            'puerta_acceso' => 'alpha_num|max:20',
            'ruta' => 'nullable|string',
            'url_imagen' => 'nullable|string'           
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        $tribuna_search = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna_search)) {
            return $this->sendError('La tribuna indicada no existe');
        }

        $localidad_search = Localidad::find($id);        
        if (is_null($localidad_search)) {
            return $this->sendError('Localidad no encontrada');
        }

        $localidad_search->nombre = $input['nombre'];
        $localidad_search->id_tribuna = $input['id_tribuna'];
        $localidad_search->puerta_acceso = $input['puerta_acceso']; 
        $localidad_search->ruta = $input['ruta'];  
        $localidad_search->url_imagen = $input['url_imagen'];         
        $localidad_search->save();

        return $this->sendResponse($localidad_search->toArray(), 'Localidad actualizada con éxito');

    }

    /**
     * Elimina un elemento de la tabla localidad
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {

            $localidad = Localidad::find($id);
            if (is_null($localidad)) {
                return $this->sendError('Localidad no encontrada');
            }
            $localidad->delete();
            return $this->sendResponse($localidad->toArray(), 'Localidad eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La localidad no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }


        
    }
}
