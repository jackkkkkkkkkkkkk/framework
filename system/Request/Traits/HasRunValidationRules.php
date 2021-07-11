<?php


namespace System\Request\Traits;


trait HasRunValidationRules
{
    protected function checkFileExists($name)
    {
        if (!$this->file($name) || $this->files[$name]['name'] === '') {
            return false;
        }
        return true;
    }

    protected function checkFirstError($name)
    {
        if (!errorExists($name) && !in_array($name, $this->errorVariablesName)) {
            return true;
        }
        return false;
    }

    protected function setError($name, $message)
    {
        array_push($this->errorVariablesName, $name);
        error($name, $message);
        $this->errorExists = true;
    }

    protected function checkFieldExists($name)
    {
        if (isset($this->request[$name]) && $this->request[$name] !== '') {
            return true;
        }
        return false;
    }

    protected function errorRedirect()
    {
        return $this->errorExists ? back() : $this->request;
    }
}