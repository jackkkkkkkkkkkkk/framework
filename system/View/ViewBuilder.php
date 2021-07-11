<?php


namespace System\View;


use System\View\traits\HasExtendsContent;
use System\View\traits\HasViewLoader;

class ViewBuilder
{
    use HasViewLoader, HasExtendsContent;
    public $content;

    public function run($dir)
    {
        try {
            $this->content = $this->viewLoader($dir);
            $this->checkExtendsContent();
        } catch (\Exception $e) {
        }
    }
}