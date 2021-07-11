<?php


namespace System\Request\Traits;


trait HasFileValidationRules
{
    protected function fileValidation($att, array $ruleArr)
    {
        foreach ($ruleArr as $rule) {
            $parts = explode(':', $rule);
            $funcName = $parts[0];
            if ($funcName == 'max' || $funcName == 'min' || $funcName == 'required') {
                $funcName .= 'File';
            }
            if (isset($parts[1])) {
                $this->$funcName($att, $parts[1]);
            } else {
                $this->$funcName($att);
            }
        }
    }

    protected function maxFile($name, $count)
    {
        if ($this->checkFileExists($name)) {
            if ($this->file($name)['size'] / 1024 > $count && $this->checkFirstError($name)) {
                $this->setError($name, "$name size must be equal or lower than $count");
            }
        }
    }

    protected function minFile($name, $count)
    {
        if ($this->checkFileExists($name)) {
            if ($this->file($name)['size'] / 1024 < $count && $this->checkFirstError($name)) {
                $this->setError($name, "$name size must be equal or higher than $count");
            }
        }
    }

    protected function requiredFile($name)
    {
        if ((!isset($this->files[$name]) || !$this->file($name)['name']) && $this->checkFirstError($name)) {
            $this->setError($name, "$name is required");
        }
    }

    protected function mimeTypes($name, $mimes)
    {
        $mimesArr = explode(',', $mimes);
        if ($this->checkFileExists($name)) {
            if ($this->checkFirstError($name)) {
                if (!in_array($this->file($name)['type'], $mimesArr)) {
                    $this->setError($name, "$name must be on of these types: $mimes ");
                }
            }
        }
    }

    protected function mimes($name, $mimes)
    {
        $mimesArr = explode(',', $mimes);
        if ($this->checkFileExists($name)) {
            if ($this->checkFirstError($name)) {
                if (!in_array(explode('/', $this->file($name)['type'])[1], $mimesArr)) {
                    $this->setError($name, "$name must be on of these types: $mimes ");
                }
            }
        }
    }
}