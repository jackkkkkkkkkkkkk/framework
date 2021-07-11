<?php


namespace App;


use System\Database\ORM\Model;

class User extends Model
{
    protected $tableName = 'users';
    protected $fillable = ['username'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'id', 'user_id', 'role_id');
    }
}