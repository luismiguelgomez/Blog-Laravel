<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JWTAuth;

class PostController extends Controller
{

 	public function index() {
 		$post = Post::all()->load('category');

 		return response()->json([
 			'code' => 200,
 			'status' => 'success',
 			'post' => $post
 		], 200);
 	}

 	public function show($id) {
 		$post = Post::find($id)->load('category');

 		if(is_object($post)){
 			$data = [
 				'code' => 200,
 				'status' => 'success',
 				'posts' => $post
 			];
 		} else {
 			$data = [
 				'code' => 404,
 				'status' => 'error',
 				'message' => 'La entrada no existe' 
 			];
 		}

 		return response()->json($data, $data['code']);
 	}

 	public function store(Request $request) {
 		//Recoger datos por POST
 		$json = $request->input('json', null);
 		$params = json_decode($json);
 		$params_array = json_decode($json, true);

 		if (!empty($params_array)) {			
	 		//Conseguir usuario identificado
 			$user = $this->getIdentity($request);

	 		//Validar los datos
 			$validate = \Validator::make($params_array, [
 				'title' => 'required',
 				'content' => 'required',
 				'category_id' => 'required',
 				'image' => 'required'
 			]);

 			if ($validate->fails()) {
 				$data = [
	 				'code' => 400,
	 				'status' => 'error',
	 				'message' => 'No se ha guardado el post, Faltan datos' 
 				];	
 			} else {
 				//Guardar el articulo
 				$post = new Post();
 				$post->user_id = $user->sub;
 				$post->category_id = $params->category_id;
 				$post->title = $params->title;
 				$post->content = $params->content;
 				$post->image = $params->image;
 				$post->save();
 				$data = [
	 				'code' => 200,
	 				'status' => 'success',
	 				'post'=> $post
 				];
 			} 
 		}else {
			$data = [
				'code' => 400,
				'status' => 'error',
				'message' => 'Envia los datos correctamente' 
			];
 		}

 		//Devolver la respuesta
 		return response()->json($data, $data['code']);
 	}

 	public function update($id, Request $request) {
 		//Recojer los datos por post
 		$json = $request->input('json', null);
 		$params_array = json_decode($json, true);

 		if (!empty($params_array)) {
 			//Validar los datos
	 		$validate = \Validator::make($params_array, [
	 			'title' => 'required',
	 			'content'=> 'required',
	 			'category_id' => 'required'
	 		]);
	 		
	 		if ($validate->fails()) {
	 			//Devolver una respuesta
		 		$data = array(
		 			'code' => 400 ,
		 			'status' => 'error',
		 			'message'=> 'Datos enviado incorrectamente'
		 		);
		 		$data['errors'] = $validate->errors();
		 		return response()->json($data, $data['code']);
	 		}

	 		//Eliminar lo que no queremos actualizar
	 		unset($params_array['id']);
	 		unset($params_array['user_id']);
	 		unset($params_array['created_at']);
	 		unset($params_array['user']);

	 		//Conseguir usuario identificado
 			$user = $this->getIdentity($request);

	 		
			//Buscar los registros
 			$post = Post::where('id', $id)
 				->where('user_id', $user->sub)
 				->first();

 			if (!empty($post) && is_object($post)) {
 				//Actualizar el registro en concreto
 				$post->update($params_array);
 				//Devolver una respuesta
		 		$data = array(
		 			'code' =>200 ,
		 			'status' => 'success',
		 			'post' => $post,
		 			'changes'=> $params_array
		 		);
 			} else {
 				//Devolver una respuesta
		 		$data = array(
		 			'code' => 400 ,
		 			'status' => 'error',
		 			'message'=> 'Datos enviado incorrectamente'
		 		);
 			}
 			/*
	 		$where = [
	 			'id' => $id,
	 			'user_id' => $user->sub
	 		];
			
	 		$post = Post::updateOrCreate($where, $params_array);
			*/

 		} else {
 			//Devolver una respuesta
	 		$data = array(
	 			'code' => 400 ,
	 			'status' => 'error',
	 			'message'=> 'Datos enviado incorrectamente'
	 		);
 		}
 		
 		

 		return response()->json($data, $data['code']);
 	}

 	public function destroy($id, Request $request) {
		//Conseguir usuario identificado	
		$user = $this->getIdentity($request);

 		//Conseguir el post
 		$post = Post::where('id', $id)
 				->where('user_id', $user->sub)
 				->first();

 		//comprobar si no esta vacio
 		if (!empty($post)) {
 			//Borrar el registro
	 		$post->delete();

	 		//Devolver respuesta
	 		$data = array(
	 			'code' => 200,
	 			'status' => 'success',
	 			'post' => $post 
	 		);
 		} else {
 			//Devolver respuesta
	 		$data = array(
	 			'code' => 404,
	 			'status' => 'error',
	 			'message' => 'No existe el post que usted escribio'  
	 		);
 		}

	 		

 		//Devolver respuesta 
 		return response()->json($data, $data['code']);
 	}

 	private function getIdentity(Request $request) {
 		//Conseguir usuario identificado	
		$jwtAuth = new JWTAuth();
		$token = $request->header('Authorization', null);
		$user = $jwtAuth->checkToken($token, true);

		return $user;
 	}

 	public function upload(Request $request) {
 		//Recoger la imagen de la petición
 		$image = $request->file('file0');

 		//Validar la imagen
 		$validate = \Validator::make($request->all(), [
 			'file0' => 'required|image|mimes:jpg,png,jpeg,jpf,gif,webp'
 		]);

 		//Guardar la imagen
 		if (!$image || $validate->fails()) {
 			echo "estoy en 229";
 			die();
 			$data = array(
 				'code' => 400,
 				'status' => 'error',
 				'message' => 'Error al subir la imagen' 
 			);
 		} else {
 			$image_name = time().$image->getClientOriginalName();
 			\Storage::disk('images')->put($image_name, \File::get($image));
 			echo "estoy en 239";
 			die();
 			

 			$data = array(
 				'code' => 200,
 				'status' => 'success',
 				'image' => $image_name
 			);

 		}

 		//Devolver datos
 		return response()->json($data, $data['code']);
 	}

}
