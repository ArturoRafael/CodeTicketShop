<?php

namespace App\Http\Controllers;

use App\Models\Auditorio;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Models\Tribuna;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Auditorio
 *
 * APIs para la gestion de auditorio
 */
class AuditorioController extends BaseController
{
     
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }


     /**
     * Lista de la tabla auditorio paginado.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
         $auditorio = Auditorio::with('pais')->with('ciudad')->with('departamento')->paginate(15);

         return $this->sendResponse($auditorio->toArray(), 'Auditorios devueltos con éxito');
    }


    /**
     * Lista de la tabla de todos los auditorio.
     *
     * @return \Illuminate\Http\Response
     */
    public function auditorio_all()
    {
        
         $auditorio = Auditorio::with('pais')
                     ->with('ciudad')
                     ->with('departamento')
                     ->get();

         return $this->sendResponse($auditorio->toArray(), 'Auditorios devueltos con éxito');
    }


    /**
     * Buscar Auditorios por nombre.
     *@bodyParam nombre string Nombre del auditorio.
     *@response{
     *    "nombre" : "auditorio 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarAuditorio(Request $request)
    {       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $auditorio = Auditorio::with('pais')
                    ->with('departamento')
                    ->with('ciudad')
                    ->with('imagens')
                ->where('auditorio.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($auditorio->toArray(), 'Todos los Auditorios filtrados');
       }else{
            
            $auditorio = Auditorio::with('pais')
                    ->with('departamento')
                    ->with('ciudad')
                    ->with('imagens')
                    ->get();
            return $this->sendResponse($auditorio->toArray(), 'Todos los Auditorios devueltos'); 
       }
        
    }


    /**
     * Listado detallado de los auditorios.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_auditorios()
    {
        
        $auditorio = Auditorio::with('tribunas')
                    ->with('eventos')
                    ->with('pais')
                    ->with('departamento')
                    ->with('ciudad')
                    ->with('imagens')
                    ->paginate(15);
        $lista_auditorio = compact('auditorio');
        return $this->sendResponse($lista_auditorio, 'Auditorios devueltos con éxito');
    }

    /**
     * Agrega un nuevo elemento a la tabla auditorio
     *@bodyParam nombre string required Nombre del Auditorio.
     *@bodyParam id_ciudad int required ID de la ciudad.
     *@bodyParam id_departamento int required ID del departamento.
     *@bodyParam id_pais int required ID del pais.
     *@bodyParam direccion string Direccion del auditorio.
     *@bodyParam longitud int Coordenada: Longitud.
     *@bodyParam latitud int Coordenada: Latitud.
     *@bodyParam aforo int Aforo.
     *@bodyParam url_imagen string Url de la imagen.
     *@bodyParam codigo_mapeado string Html de la imagen.
     *@response{
     *       "nombre" : "Auditorio 1",
     *       "id_ciudad" : 1,
     *       "id_departamento": 1,
     *       "id_pais": 1,
     *       "direccion": "Street 1-56",
     *       "longitud": null,
     *       "latitud": null,
     *       "aforo": null,
     *       "url_imagen":null,
     *       "codigo_mapeado":null
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(), [
            'nombre' => 'required',   
            'id_ciudad' => 'required|integer',
            'id_departamento' => 'required|integer',
            'id_pais' => 'required|integer',
            'direccion' => 'required',
            'longitud' => 'nullable|numeric',
            'latitud' => 'nullable|numeric',
            'aforo' => 'nullable|integer',
            'url_imagen' => 'nullable|string',
            'codigo_mapeado' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $pais = Pais::find($request->input('id_pais'));
        if (is_null($pais)) {
            return $this->sendError('El País indicado no existe');
        }

        $departamento = Departamento::find($request->input('id_departamento'));
        if (is_null($departamento)) {
            return $this->sendError('El Departamento indicado no existe');
        }

        $ciudad = Ciudad::find($request->input('id_ciudad'));
        if (is_null($ciudad)) {
            return $this->sendError('La Ciudad indicada no existe');
        }
        
        $auditorio=Auditorio::create($request->all());        
        return $this->sendResponse($auditorio->toArray(), 'Auditorio creado con éxito');

    }

    /**
     * Lista un auditorio en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
         $auditorio = Auditorio::with('tribunas')->with('pais')->with('ciudad')->with('departamento')->find($id);


        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }


        return $this->sendResponse($auditorio->toArray(), 'Auditorio devuelto con éxito');
    }


    /**
     * Localidades por auditorio 
     *
     * [Se filtra por el ID del auditorio]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function localidades_auditorio($id)
    {

        $auditorio = Auditorio::find($id);
        if (!$auditorio) {
            return $this->sendError('Auditorio no encontrado');
        }

        $auditorios = Tribuna::with('auditorio')
                      ->with('localidads')
                      ->where('id_auditorio', $id)
                      ->first();

        if (is_null($auditorios)) {
            return $this->sendError('El auditorio no posee localidades');
        }

        $local_aud = array();
        
            
        array_push($local_aud, ["auditorio" => $auditorios['auditorio'], "localidades" => $auditorios['localidads']]);
       

        return $this->sendResponse($local_aud, 'Localidades por auditorio devueltas con éxito');
    }

  
   /**
     * Actualiza un elemeto de la tabla auditorio 
     *
     * [Se filtra por el ID]
     *
     *@bodyParam nombre string required Nombre del Auditorio.
     *@bodyParam id_ciudad int required ID de la ciudad.
     *@bodyParam id_departamento int required ID del departamento.
     *@bodyParam id_pais int required ID del pais.
     *@bodyParam direccion string Direccion del auditorio.
     *@bodyParam longitud int Coordenada: Longitud.
     *@bodyParam latitud int Coordenada: Latitud.
     *@bodyParam aforo int Aforo.
     *@bodyParam url_imagen string Url de la imagen.
     *@bodyParam codigo_mapeado string Html de la imagen.
     *@response{
     *       "nombre" : "Auditorio GOLD",
     *       "id_ciudad" : 1,
     *       "id_departamento": 2,
     *       "id_pais": 1,
     *       "direccion": "Street 1-56",
     *       "longitud": 222,
     *       "latitud": 765,
     *       "aforo": 1000,
     *       "url_imagen":null,
     *       "codigo_mapeado":null
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auditorio  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        //
         $input = $request->all();


        $validator = Validator::make($request->all(), [
            'nombre' => 'required',   
            'id_ciudad' => 'required|integer',
            'id_departamento' => 'required|integer',
            'id_pais' => 'required|integer',
            'direccion' => 'required',
            'longitud' => 'nullable|numeric',
            'latitud' => 'nullable|numeric',
            'aforo' => 'nullable|integer',
            'url_imagen' => 'nullable|string',
            'codigo_mapeado' => 'nullable|string'           
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }


        $pais = Pais::find($request->input('id_pais'));
        if (is_null($pais)) {
            return $this->sendError('El País indicado no existe');
        }

        $departamento = Departamento::find($request->input('id_departamento'));
        if (is_null($departamento)) {
            return $this->sendError('El Departamento indicado no existe');
        }

        $ciudad = Ciudad::find($request->input('id_ciudad'));
        if (is_null($ciudad)) {
            return $this->sendError('La Ciudad indicada no existe');
        }

         $auditorio = Auditorio::find($id);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }
        
        $auditorio->nombre = $input['nombre'];
        $auditorio->ciudad = $input['id_ciudad'];
        $auditorio->departamento = $input['id_departamento'];
        $auditorio->pais = $input['id_pais'];
        $auditorio->direccion = $input['direccion'];        
        if (!is_null($request->input('latitud'))) 
            $auditorio->latitud = $input['latitud'];
        
        if (!is_null($request->input('longitud'))) 
            $auditorio->longitud = $input['longitud'];
        
        if (!is_null($request->input('aforo'))) 
            $auditorio->aforo = $input['aforo'];
        
        if (!is_null($request->input('codigo_mapeado'))) 
            $auditorio->codigo_mapeado = $input['codigo_mapeado'];
        
        if (!is_null($request->input('url_imagen'))) 
            $auditorio->url_imagen = $input['url_imagen'];

         $auditorio->save();

        return $this->sendResponse($auditorio->toArray(), 'Auditorio actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla auditorio
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Auditorio  $auditorio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $auditorio =Auditorio::find($id);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }
        $auditorio->delete();


        return $this->sendResponse($auditorio->toArray(), 'Auditorio eliminado con éxito');
    }
}
