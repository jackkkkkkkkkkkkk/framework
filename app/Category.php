<?php


namespace App;


use System\Database\ORM\Model;

class Category extends Model
{
    protected $tableName = 'categories';
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'cat_id', 'id');
    }
}