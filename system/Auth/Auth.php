<?php


namespace System\Auth;


use App\User;
use System\Session\Session;

class Auth
{
    private $redirectTo = '/login';

    private function userMethod()
    {
        $userId = Session::get('user');
        if (!$userId) {
            return false;
        }
        $user = User::find($userId);
        if (empty($user)) {
            Session::remove($userId);
            return false;
        }
        return $user;
    }

    private function checkMethod()
    {
        $userId = Session::get('user');
        if (!$userId) {
            return false;
        }
        $user = User::find($userId);
        if (empty($user)) {
            Session::remove($userId);
            return false;
        }
        return true;
    }

    private function loginByIdMethod($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return false;
        }
        Session::set('user', $id);
        return true;
    }

    private function loginByEmailMethod($email, $password)
    {
        $user = User::where('email', $email)->get();
        if (empty($user)) {
            return false;
        }
        if ($user[0]->active === false || !password_verify($password, $user[0]->password)) {
            return false;
        }
        Session::set('user', $user[0]->id);
        return true;
    }

    private function logoutMethod()
    {
        Session::remove('user');
        return true;
    }

    public function __call($name, $arguments)
    {
        $this->methodCaller($name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = new self();
        return $instance->methodCaller($name, $arguments);
    }

    private function methodCaller($name, $arguments)
    {
        $suffix = 'Method';
        $method = $name . $suffix;
        return call_user_func_array(array($this, $method), $arguments);
    }
}