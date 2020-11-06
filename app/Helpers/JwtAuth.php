<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Iluminate\Support\Facades\DB;
use App\User;

class JwtAuth {

	public $key;

	public function __construct() {
		$this -> key = 'esto_es_una_clave_super_989874';
	}
	
	public function singup($email, $password, $getToken = null) {
		//Buscar si existe usuairo con sus credenciales
		$user = User::where([
			'email' => $email,
			'password' => $password
		])->first();

		//Comprobar si son correctas
		$singup = false;
		if (is_object($user)) {
			$singup = true;
		}
		
		//Generar el token con los datos del usuario identificado
		if ($singup){
			$token =  array(
				'sub' => $user->id,
				'email' => $user->email,
				'name' => $user->name,
				'surname' => $user->surname,
				'iat' => time(),
				'exp' => time() + (7 * 24 * 60 * 60)
			);

			$jwt = JWT::encode($token, $this->key, 'HS256');
			$decode = JWT::decode($jwt, $this->key, ['HS256']);

			//Devolver los datos decodificados o token en funcion de un parametro	
			if(is_null($getToken)){
				$data = $jwt;
			} else {
				$data = $decode;
			}

		} else {
			$data = array(
				'status' => 'error',
				'message' => 'Login incorrecto' 
			);
		}
		
		
		return $data;
	}


	public function checkToken($jwt, $getIdentity = false) {
		$auth = false;

		try {
			$jwt = str_replace('"', '', $jwt);
			$decoded = JWT::decode($jwt, $this->key, ['HS256']); 
		} catch (\UnexpectedValueException $e) {
			echo "se rompio en catch 1";
			$auth = false;
		} catch (\DomainException $e) {
			echo "se rompio en catch 2";
			$auth = false;
		}

		if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
			$auth = true;
		} else {
			$auth = false;
		}

		if ($getIdentity) {
			return $decoded;
		}

		return $auth;
	}

}
