<?php

namespace sisUsuarios;

use Illuminate\Database\Eloquent\Model;

class Rol_user extends Model
{
	protected $table='rol_users';
    protected $primaryKey='id';
    public $timestamps=false;

    protected $fillable=[
      'name',
	  'status'
    ];

     protected $guarded=[
    ];
}
