<?php


namespace System\Database\ORM;


use System\Database\Traits\HasAttributes;
use System\Database\Traits\HasCRUD;
use System\Database\Traits\HasMethodCaller;
use System\Database\Traits\HasQueryBuilder;
use System\Database\Traits\HasRelation;

abstract class Model
{
    use HasQueryBuilder;
    use HasAttributes;
    use HasCRUD;
    use HasMethodCaller;
    use HasRelation;
    protected $tableName;
    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];
    protected $collection = [];
    protected $createdAt = 'created_at';
    protected $updatedAt = 'updated_at';
    protected $primaryKey = 'id';
}