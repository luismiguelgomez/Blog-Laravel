<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //Tabla de la base de datos
    protected $table = 'posts';

    protected $fillable = [
    	'title', 'content', 'category_id'
    ];


    //Relacion de muchos a uno
    public function user() {
    	return $this->belongsTo ('App\User', 'user_id');
    }

    public function category() {
    	return $this-> belongsTo ('App\Category', 'category_id');
    }

}
