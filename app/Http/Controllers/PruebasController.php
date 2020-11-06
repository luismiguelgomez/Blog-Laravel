<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Con este ya podemos utilizar la clase y modelo de Post
use App\Post;
//Con este ya podemos utilizar la clase y modelo de Category
use App\Category;

class PruebasController extends Controller
{
    public function index(){
    	$titulo = 'Animales';
    	$animales = ['perro','gato','trigre'];

    	return view('pruebas.index' , array(
    		'titulo' => $titulo,
    		'animales' => $animales,
    	));
    }

    public function testOrm(){

    	//EJEMPLO CON POST
    	/*
    	$posts = Post::all();

    	//Obtiene los titulos de los post
    	foreach ($posts as $post) {
    		echo "<h1>" .$post->title . "</h1>";
    		echo "<span style='color:gray;'>{$post->category->name} -{$post->user->name} </span>";
    		echo "<p>" .$post->content . "</p>";
    		echo "<hr>";
    	}
		*/

		/*
    	//El var_dump revisa la info que tiene la variable, en este caso post
    	var_dump($post);
		*/

		$categories = Category::all();

		foreach ($categories as $category) {
			echo "<h1>  $category->name </h1>";

			foreach ($category->posts as $post) {
	    		echo "<h3>" .$post->title . "</h3>";
	    		echo "<span style='color:gray;'>{$post->category->name} -{$post->user->name} </span>";
	    		echo "<p>" .$post->content . "</p>";
    		}
    		echo "<hr>";
		}

    	//evita la vista
    	die();
    }
}