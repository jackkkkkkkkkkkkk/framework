<?php


namespace System\View\traits;



trait HasViewLoader
{
    private $viewNameArray = [];

    private function viewLoader($dir)
    {
        $viewPath = BASE_DIR . 'resources/view/' . str_replace('.', '/', $dir) . ".blade.php";
        if (file_exists($viewPath)) {
            $this->registerView($viewPath);
            $content = file_get_contents($viewPath);
            $htmlContent = htmlentities($content);
            return $htmlContent;
        } else {
            throw new \Exception("view doesn't exists");
        }
    }

    private function registerView($view)
    {
        array_push($this->viewNameArray, $view);
    }
}