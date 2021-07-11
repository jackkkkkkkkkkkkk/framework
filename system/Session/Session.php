<?php


namespace System\Session;


class Session
{
    private function get($name)
    {
        if (isset($_SESSION[$name]))
            return $_SESSION[$name];
        return false;
    }

    private function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    private function remove($name)
    {
        if (isset($_SESSION[$name]))
            unset($_SESSION[$name]);
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = new self();
        return call_user_func_array([$instance, $name], $arguments);
    }
}