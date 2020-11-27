<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function pruebas(Request $request) {
    	return "Accion de pruebas de USER - CONTROLLER";
    }

    public function register(Request $request) {

    	//Recoger los datos del usuario por post
    	$json = $request->input('json', null);
    	$params = json_decode($json);//objeto
    	$params_array = json_decode($json, true); //array

    	if (!empty($params) && !empty($params_array)) {
    	
			//Limpiar datos
	    	$params_array = array_map('trim', $params_array);

	    	//validar datos del usuario
	    	//\Validator, es un alias de la libreria "Validator"
	    	$validate = \Validator::make($params_array, [
	    		'name' => 'required|alpha',
	    		'surname' => 'required|alpha',
	    		'email' => 'required|email|unique:users',
	    		'password' => 'required'
	    	]);

	    	if ($validate->fails()) {
	    		//La validacion a fallado
	    		$data = array(
	    		'status' => 'error',
	    		'code' => 404,
	    		'message' => 'El usuario no se ha creado',
	    		'errors' => $validate->errors()
	    	);
	    		
	    	} else {
	    		//validacion exitosa
	    		
	    		//Cifrar la contraseña
	    		$pwd = hash('sha256', $params->password);

    			//Crear el usuario
                
	    		$user = new User();
	    		$user -> name = $params_array['name'];
	    		$user -> surname = $params_array['surname'];
	    		$user -> email = $params_array['email'];
	    		$user -> password = $pwd;
	    		$user -> role = 'ROLE_USER';

	    		//Guardar el usuario
	    		$user->save();

	    		$data = array(
	    		'status' => 'success',
	    		'code' => 200,
	    		'message' => 'El usuario se ha creado correctamente',
	    		'user' => $user
	    		);
	    	}
		} else {
			$data = array(
	    		'status' => 'error',
	    		'code' => 404,
	    		'message' => 'Los datos enviados no son correctos',
	    	);
		}

    	return response()->json($data, $data['code']);

    	/* Ejemplo de registro basico, para ser consultado
    	se utilizo postman
    	$name = $request->input('name');
    	$surname = $request->input('surname');
    	return "Accion de Registro de usuarios: $name $surname";
    	*/
    }

    public function login(Request $request) {
    	$jwtAuth = new \JwtAuth();

    	//Recibir datos por POST
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);
    	
    	//Validar los datos
    	$validate = \Validator::make($params_array, [
	    		'email' => 'required|email',
	    		'password' => 'required'
	    	]);

	    	if ($validate->fails()) {
	    		//La validacion a fallado
	    		$singup = array(
	    		'status' => 'error',
	    		'code' => 404,
	    		'message' => 'El usuario no se ha podido loguear',
	    		'errors' => $validate->errors()
	    	);
	    		
	    	} else {
				//Cifrar la password
				$pwd = hash('sha256', $params->password);
    			//devolvver token o datos
    			$singup = $jwtAuth->singup($params->email, $pwd);

    			if (!empty($params->gettoken)){
    				$singup = $jwtAuth->singup($params->email,$pwd, true);
    			}

			}
    	
    	return response()->json($singup, 200);
    }

    public function update(Request $request) {

        //Recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {

            //Sacar usuario identificado
            $user= $jwtAuth->checkToken($token, true);

            //Validar datos
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users,' .$user->sub
            ]);

            //Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            
            //Actualizar usuario en bbdd
            $user_update = User::where('id', $user->sub)->update($params_array);

            //Devolver array con resultado
           
           $data = array(
                'code' =>200 ,
                'status' => 'success',
                'message' => $user,
                'changes' => $params_array
            );
           
        } else {
            $data = array(
                'code' =>400 ,
                'status' => 'error',
                'message' => 'El usuario no esta identificado.' 
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        // Recojer los datos de la petición
        //'file0' -> sera el nombre de los archivos
        $image = $request->file('file0');

        // Guardar la imagen
        if ($image) {
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200 ,
                'status' => 'success',
                'image' => $image_name
            );
        } else {
              //Devolver el resultado
            $data = array(
                'code' =>400 ,
                'status' => 'error',
                'message' => 'Error al subir imagen' 
            );

        }

      
        return response()->json($data, $data['code']);
    }
}
