<?php


namespace System\Router;


use ReflectionMethod;

class Routing
{
    private $currentUrl;
    private $methodField;
    private $routes;
    private $values = [];

    public function __construct()
    {
        $this->currentUrl = explode('/', CURRENT_URI);
        global $routes;
        $this->routes = $routes;
        $this->methodField = $this->methodField();
    }

    public function run()
    {
        $match = $this->match();

        if (!$match) {
            $this->error404();
            return;
        }
        $controllerPath = BASE_DIR . '/app/Http/Controllers/' . $match['class'] . '.php';
        $controller = '\App\Http\Controllers\\' . $match['class'];
        if (!file_exists($controllerPath)) {
            $this->error404();
            return;
        }
        $obj = new $controller();
        $method = $match['method'];
        if (!method_exists($obj, $method)) {
            $this->error404();
            return;
        }
        try {
            $reflection = new ReflectionMethod($controller, $method);
            if ($reflection->getNumberOfParameters() == count($this->values)) {
                call_user_func_array(array($obj, $method), $this->values);
                return;
            } else {
                $this->error404();
                return;
            }
        } catch (\ReflectionException $e) {
        }

    }

    public function match()
    {
        $reservedRoutes = $this->routes[$this->methodField];
        foreach ($reservedRoutes as $r) {
            if ($this->compare($r['url'])) {
                return ['class' => $r['class'], 'method' => $r['method']];
            }
        }
        return [];
    }

    public function compare($reservedUrl)
    {
        $reservedUrl = explode('/', trim($reservedUrl, '/'));
        if (sizeof($reservedUrl) != sizeof($this->currentUrl)) {
            $this->values = [];
            return false;
        }
        foreach ($reservedUrl as $k => $r) {
            if (substr($r, 0, 1) == '{' && substr($r, 0, -1) == '}') {
                array_push($this->values, $this->currentUrl[$k]);
            } elseif ($r !== $this->currentUrl[$k]) {
                $this->values = [];
                return false;
            }
        }
        return true;

    }

    private function methodField()
    {

        if (isset($_POST['_method'])) {
            if ($_POST['_method'] == 'put') {
                return 'put';
            } else if ($_POST['_method'] == 'delete') {
                return 'delete';
            } else {
                return 'post';
            }
        }
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function error404()
    {
        http_response_code(404);
        include __DIR__ . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . '404.php';
        exit;
    }
}