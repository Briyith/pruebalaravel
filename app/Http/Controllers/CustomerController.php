<?php

namespace sisUsuarios\Http\Controllers;

use Illuminate\Http\Request;

use sisUsuarios\Http\Requests;
use sisUsuarios\Helpers\JwtAuth;
use sisUsuarios\Customer;

class CustomerController extends Controller
{
	public $idRoles;

	public function __construct(){
		$this->idRoles = array(1,2);
	}

    public function index(Request $request){
    	$customers = Customer::all();
    	return response()->json(array(
    		'customers' => $customers,
    		'status' =>'succes'
    	), 200);
    }


    public function show($id){
    	$customer = Customer::find($id);
    	return response()->json(array(
    		'customer'=> $customer,
    		'status'=> 'success'
    	),200);
      
    }


    public function store(Request $request){
    	$hash = $request->header('Authorization',null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);
    	if($checkToken){
    		//Recoger datos popr POST
    		$json = $request->input('json', null);
 			$params = json_decode($json);
 			$params_array = json_decode($json, true);

 			//Conseguir el usuario identificado
 			$user = $jwtAuth->checkToken($hash,true);

 			// Validación
 			$validate = \Validator::make($params_array,[
 				'name' => 'required|min:5',
 				'email' => 'required',
 				'document' => 'required',
 				'address' => 'required'
 			]);
 			
 			if($validate->fails()){
 				return response()->json($validate->errors(),400);
 			}


			// Evaluamos si el usuario cumple con el rol
			if(in_array($user->role,$this->idRoles)){

				// Evaluamos que no sea existente.
				$isset_user = Customer::where('email', '=', $params->email)->first();

	 			if(is_null($isset_user)){
		 			//Crear el Cliente.
		 			$customer = new Customer();
		 			$customer->name = $params->name;
		 			$customer->document = $params->document;
		 			$customer->address = $params->address;
		 			$customer->email = $params->email;
	 				// Guardar Cliente.
	 				$customer->save();
	 				$data = array(
	 					'customer' => $customer,
		 				'status'=>'success',
		 				'code' => 200,
		 				'message' => 'Cliente registrado correctamente.'
		 			);

	 			}else{
	 				// No guardarlo porque ya existe.
	 				$data = array(
		 				'status'=>'error',
		 				'code' => 400,
		 				'message' => 'Cliente duplicado, no puede registrarse.'
	 				);
	 			}
	 		}else{
 				// No guardarlo porque no cumple con el rol.
 				$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Cliente no cumple con los permisos.'
 				);
	 		}

    	}else{
    		$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Login incorrecto.'
 				);
    	}

    	return response()->json($data,200);
      
    }


    public function update($id, Request $request){
    	$hash = $request->header('Authorization',null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);
    	if($checkToken){
    		//Recoger datos popr POST
    		$json = $request->input('json', null);
 			$params = json_decode($json);
 			$params_array = json_decode($json, true);

 			//Conseguir el usuario identificado
 			$user = $jwtAuth->checkToken($hash,true);

 			// Validación
 			$validate = \Validator::make($params_array,[
 				'name' => 'required|min:5',
 				'document' => 'required',
 				'email' => 'required',
 				'address' => 'required'
 			]);
 			
 			if($validate->fails()){
 				return response()->json($validate->errors(),400);
 			}


		
			// Evaluamos si el usuario cumple con el rol
			if(in_array($user->role,$this->idRoles)){

				// Actualizar registro.
				$customer = Customer::where('id',$id)->update($params_array);

	 				$data = array(
	 					'user' => $params,
		 				'status'=>'success',
		 				'code' => 200,
		 				'message' => 'Cliente actualizado Correctamente.'
		 			);
	 		}else{
 				// No guardar porque no cumple con los permisos.
 				$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Cliente no cumple con los permisos.'
 				);
	 		}

    	}else{
    		$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Login incorrecto.'
 				);
    	}

    	return response()->json($data,200);
      
    }

    public function destroy($id, Request $request){
    	$hash = $request->header('Authorization',null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);
    	if($checkToken){
    		//Recoger datos popr POST
    		$json = $request->input('json', null);
 			$params = json_decode($json);
 			$params_array = json_decode($json, true);

 			//Conseguir el usuario identificado
 			$user = $jwtAuth->checkToken($hash,true);

 			// Validación
 			$validate = \Validator::make($params_array,[
 				'name' => 'required|min:5',
 				'document' => 'required',
 				'email' => 'required',
 				'address' => 'required'
 			]);
 			
 			if($validate->fails()){
 				return response()->json($validate->errors(),400);
 			}


		
			// Evaluamos si el usuario cumple con el rol
			if(in_array($user->role,$this->idRoles)){

				// Comprobar que existe el registro.
				$customer = Customer::find($id);

				// Borrarlo
				$customer->delete();

				// Devolverlo
 				$data = array(
 					'customer' => $customer,
	 				'status'=>'success',
	 				'code' => 200,
	 				'message' => 'Cliente eliminado Correctamente.'
	 			);

	 		}else{
 				// No guardarlo porque no cumple con los permisos.
 				$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Cliente no cumple con los permisos.'
 				);
	 		}

    	}else{
    		$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Login incorrecto.'
 				);
    	}

    	return response()->json($data,200);
      

    }
}



