<?php


namespace App;


use System\Database\ORM\Model;

class Role extends Model
{
    protected $tableName = 'roles';
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class,'user_role','id','role_id','user_id');
    }
}