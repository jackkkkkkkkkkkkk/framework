<?php


namespace System\View\traits;


use function strpos;

trait HasExtendsContent
{
    private $extendsContent;

    private function checkExtendsContent()
    {
        $layoutPath = $this->findExtends();
        if ($layoutPath) {
            $this->extendsContent = $this->viewLoader($layoutPath);
            $yieldsArray = $this->findYields();
            if ($yieldsArray) {
                foreach ($yieldsArray as $yield) {
                    $this->initialYield($yield, strpos());
                }
            }
        }
    }

    private function findExtends()
    {
        $pathArray = [];
        preg_match("/\s*extends\('(.*)'\)/", $this->content, $matches);
        return isset($matches[1]) ? $matches[1] : false;
    }

    private function findYields()
    {
        $pathArray = [];
        preg_match_all("/\s*yield\('(.*)'\)/", $this->content, $matches, PREG_UNMATCHED_AS_NULL);
        return isset($matches[1]) ? $matches[1] : false;
    }

    private function initialYield($yield, $strpos)
    {
        $string = $this->content;
        $startWord = "@section('" . $yield . "')";
        $endWord = "@endsection";
        $startPos = strpos($string, $startWord);
        if ($startPos === false) {
            $this->extendsContent = str_replace("yield('$yield')", '', $this->extendsContent);
            return $this->extendsContent;
        }
        $startPos += strlen($startWord);
        $endPos = strpos($string, $endWord) - 1;
    }
}