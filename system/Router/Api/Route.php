<?php


namespace System\Router\Api;


class Route
{
    public static function get($url, $executeMethod, $name = null)
    {
        list($class, $method) = explode('@', $executeMethod);
        global $routes;
        array_push($routes['get'], ['url' => 'api/' . trim($url, '/'), 'class' => $class, 'method' => $method, 'name' => $name]);
    }

    public static function post($url, $executeMethod, $name = null)
    {
        list($class, $method) = explode('@', $executeMethod);
        global $routes;
        array_push($routes['post'], ['url' => 'api/' . trim($url, '/'), 'class' => $class, 'method' => $method, 'name' => $name]);
    }

    public static function put($url, $executeMethod, $name = null)
    {
        list($class, $method) = explode('@', $executeMethod);
        global $routes;
        array_push($routes['put'], ['url' => trim($url, '/'), 'class' => $class, 'method' => $method, 'name' => $name]);
    }

    public static function delete($url, $executeMethod, $name = null)
    {
        list($class, $method) = explode('@', $executeMethod);
        global $routes;
        array_push($routes['delete'], ['url' => trim($url, '/'), 'class' => $class, 'method' => $method, 'name' => $name]);
    }


}