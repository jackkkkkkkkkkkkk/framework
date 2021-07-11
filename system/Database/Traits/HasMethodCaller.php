<?php


namespace System\Database\Traits;


trait HasMethodCaller
{
    private $allMethods = ['create', 'update', 'delete', 'find', 'all', 'save', 'where', 'whereOr', 'whereNull', 'whereNotNull', 'whereIn', 'orderBy', 'limit', 'get', 'paginate'];
    private $allowedMethods = ['create', 'update', 'delete', 'find', 'all', 'save', 'where', 'whereOr', 'whereNull', 'whereNotNull', 'whereIn', 'orderBy', 'limit', 'get', 'paginate'];

    public function __call($name, $arguments)
    {
        return $this->methodCaller($this, $name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $className = get_called_class();
        $instance = new $className;
        return $instance->methodCaller($instance, $name, $arguments);
    }

    protected function methodCaller($object, $methodName, $args)
    {
        if (in_array($methodName, $this->allowedMethods)) {
            $suffix = 'Method';
            $methodName .= $suffix;
            return call_user_func_array(array($object, $methodName), $args);
        }
    }

    protected function setAllowedMethods(array $array)
    {
        $this->allowedMethods = $array;
    }
}