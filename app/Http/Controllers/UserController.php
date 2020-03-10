<?php

namespace sisUsuarios\Http\Controllers;

use Illuminate\Http\Request;

use sisUsuarios\Helpers\JwtAuth;
use sisUsuarios\Http\Requests;
use Illuminate\Support\Facades\DB;
use sisUsuarios\User;


class UserController extends Controller
{
	public function register(Request $request){
 		//Recoger post
 		$json = $request->input('json', null);
 		$params = json_decode($json);

 		$email=(!is_null($json) && isset($params->email)) ? $params->email : null;
 		$name = (!is_null($json) && isset($params->name)) ? $params->name : null;
 		$surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
 		$role = (!is_null($json) && isset($params->role)) ? $params->role : null;
 		$password = (!is_null($json) && isset($params->password)) ? $params->password : null;


 		if(!is_null($email) && !is_null($password) && !is_null($name) && !is_null($role)){

 			//Crear el usuario.
 			$user = new User();
 			$user->email = $email;
 			$user->name = $name;
 			$user->surname = $surname;
 			$user->role = $role;

 			$pwd = hash('sha256', $password);
 			$user->password = $pwd;

 			

 			// Comprobar usuario duplicado.
 			$isset_user = User::where('email', '=', $email)->first();

 			if(is_null($isset_user)){
 				// Guardar usuario.
 				$user->save();
 				$data = array(
	 				'status'=>'success',
	 				'code' => 200,
	 				'message' => 'Usuario registrado correctamente.'
	 			);

 			}else{
 				// No guardarlo porque ya existe.
 				$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Usuario duplicado, no puede registrarse.'
 				);
 			}

 		}else{
 			$data = array(
 				'status'=>'error',
 				'code' => 400,
 				'message' => 'Usuario no creado'
 			);
 		}

 		return response()->json($data, 200);
 	}


 	public function login(Request $request){
 		$jwtAuth = new JwtAuth();

 		//Recibir POST
 		$json = $request->input('json', null);
 		$params = json_decode($json);

 		$email=(!is_null($json) && isset($params->email)) ? $params->email : null;
 		$password = (!is_null($json) && isset($params->password)) ? $params->password : null;
 		$role = (!is_null($json) && isset($params->role)) ? $params->role : null;
 		$getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;


 		//Cifrar password
 		$pwd = hash('sha256', $password);

 		if($role == 1 || $role == 2 ){

	 		if(!is_null($email) && !is_null($password) && is_null($getToken)){
	 			$signup  = $jwtAuth->signup($email, $pwd);

	 		}elseif($getToken != null){
	 			
	 			$signup = $jwtAuth->signup($email, $pwd, $getToken);
	 		
	 		}else{
	 			$signup = array(
	 				'status' => 'error',
	 				'message' =>'Envia tus datos por post'
	 			);
	 		}

	 		return response()->json($signup,200);

	 	}else{
	 		echo "Rol de usuario no permitido";
	 	}
	}


	public function index(Request $request){
    	$users = User::all();
    	return response()->json(array(
    		'users' => $users,
    		'status' =>'succes'
    	), 200);
    }

    public function show($id){
    	$user = User::find($id);
    	return response()->json(array(
    		'user'=> $user,
    		'status'=> 'success'
    	),200);
      
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
 				'email' => 'required',
 				'password' => 'required',
 				'role' => 'required'
 			]);
 			
 			if($validate->fails()){
 				return response()->json($validate->errors(),400);
 			}


		
			// Evaluamos si el usuario cumple con el rol
			if($user->role == 1){

				// Actualizar registro.
				$userUp = User::where('id',$id)->update($params_array);

	 				$data = array(
	 					'user' => $params,
		 				'status'=>'success',
		 				'code' => 200,
		 				'message' => 'Usuario actualizado Correctamente.'
		 			);
	 		}else{
 				// No guardar porque no cumple con los permisos.
 				$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Usuario no cumple con los permisos.'
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
 				'email' => 'required',
 				'password' => 'required',
 				'role' => 'required'
 			]);
 			
 			if($validate->fails()){
 				return response()->json($validate->errors(),400);
 			}


		
			// Evaluamos si el usuario cumple con el rol
			if($user->role == 1){

				// Comprobar que existe el registro.
				$userDl = User::find($id);

				// Borrarlo
				$userDl->delete();

				// Devolverlo
 				$data = array(
 					'user' => $userDl,
	 				'status'=>'success',
	 				'code' => 200,
	 				'message' => 'Usuario eliminado Correctamente.'
	 			);
	 		}else{
 				// No guardarlo porque no cumple con los permisos.
 				$data = array(
	 				'status'=>'error',
	 				'code' => 400,
	 				'message' => 'Usuario no cumple con los permisos.'
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

    public function logout(Request $request) {
    	// cerrar login.

    }



}

