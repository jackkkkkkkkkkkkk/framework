<?php


namespace System\Request\Traits;


use DateTime;
use System\Database\DBConnection\DBConnection;

trait HasValidationRules
{
    protected function numberValidation($att, array $ruleArr)
    {
        foreach ($ruleArr as $rule) {
            $parts = explode(':', $rule);
            $funcName = $parts[0];
            if ($funcName == 'max' || $funcName == 'min') {
                $funcName .= 'Number';
            }
            if (isset($parts[1])) {
                $this->$funcName($att, $parts[1]);
            } else {
                $this->$funcName($att);
            }
        }
    }

    protected function normalValidation($att, array $ruleArr)
    {
        foreach ($ruleArr as $rule) {
            $parts = explode(':', $rule);
            $funcName = $parts[0];
            if ($funcName == 'max' || $funcName == 'min') {
                $funcName .= 'Str';
            }
            if (isset($parts[1])) {
                $this->$funcName($att, $parts[1]);
            } else {
                $this->$funcName($att);
            }
        }
    }


    protected function maxStr($name, $count)
    {
        if ($this->checkFieldExists($name)) {
            if (strlen($this->request[$name]) > $count && $this->checkFirstError($name)) {
                $this->setError($name, "$name len must be equal or lower than $count");
            }
        }
    }

    protected function minStr($name, $count)
    {
        if ($this->checkFieldExists($name)) {
            if (strlen($this->request[$name]) < $count && $this->checkFirstError($name)) {
                $this->setError($name, "$name len must be equal or lower than $count");
            }
        }
    }

    protected function maxNumber($name, $count)
    {
        if ($this->checkFieldExists($name)) {
            if ($this->request[$name] > $count && $this->checkFirstError($name)) {
                $this->setError($name, "$name len must be equal or lower than $count");
            }
        }
    }

    protected function minNumber($name, $count)
    {
        if ($this->checkFieldExists($name)) {
            if ($this->request[$name] < $count && $this->checkFirstError($name)) {
                $this->setError($name, "$name len must be equal or lower than $count");
            }
        }
    }

    protected function required($name)
    {
        if ((!isset($this->request[$name]) || $this->request[$name] === '') && $this->checkFirstError($name)) {
            $this->setError($name, "$name is required");
        }
    }

    protected function number($name)
    {
        if ($this->checkFieldExists($name)) {
            if (!is_numeric($this->request[$name]) && $this->checkFirstError($name)) {
                $this->setError($name, "$name must be a number");
            }
        }
    }

    protected function email($name)
    {
        if ($this->checkFieldExists($name)) {
            if (!filter_var($this->request[$name], FILTER_VALIDATE_EMAIL) && $this->checkFirstError($name)) {
                $this->setError("$name must be an email");
            }
        }
    }

    protected function date($name, $format = 'Y-m-d')
    {
        if ($this->checkFieldExists($name)) {
            $d = DateTime::createFromFormat($format, $this->request[$name]);
            if (!($d && $d->format($format) === $this->request[$name]))
                $this->setError($name, "$name must be a date");
        }
    }

    protected function exists($name, $args)
    {
        $args = explode(',', $args);
        $table = isset($args[0]) ? $args[0] : null;
        $field = isset($args[1]) ? $args[1] : 'id';
        if ($this->checkFieldExists($name)) {
            if ($this->checkFirstError($name)) {
                $sql = "SELECT count(*) FROM $table WHERE $field=?";
                $stmt = DBConnection::getInstance()->prepare($sql);
                $stmt->execute([$this->$name]);
                $result = $stmt->fetchColumn();
                if (!$result) {
                    $this->setError($name, "$name does'nt exist in $table");
                }
            }
        }
    }
}