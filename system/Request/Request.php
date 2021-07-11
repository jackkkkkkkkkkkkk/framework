<?php


namespace System\Request;


use System\Request\Traits\HasFileValidationRules;
use System\Request\Traits\HasRunValidationRules;
use System\Request\Traits\HasValidationRules;

class Request
{
    use HasValidationRules, HasFileValidationRules, HasRunValidationRules;
    private $request;
    private $files;
    private $errorVariablesName;
    private $errorExists = false;

    public function __construct()
    {
        if (!empty($_POST)) {
            $this->postAttributes();
        }
        if (!empty($_FILES)) {
            $this->files = $_FILES;
        }
        $rules = $this->rules();
        empty($rules) ?: $this->run($rules);
        $this->errorRedirect();
    }

    public function file($name)
    {
        return isset($this->files[$name]) ? $this->files[$name] : false;
    }

    private function postAttributes()
    {
        foreach ($_POST as $key => $value) {
            $this->$key = htmlentities($value);
            $this->request[$key] = htmlentities($value);
        }
    }

    public function all()
    {
        return $this->request;
    }

    private function rules()
    {
        return [];
    }

    private function run($rules)
    {
        foreach ($rules as $att => $rule) {
            $ruleArr = explode($rule, '|');
            if (in_array('file', $ruleArr)) {
                unset($ruleArr[array_search('file', $ruleArr)]);
                $this->fileValidation($att, $ruleArr);
            } else if (in_array('number', $ruleArr)) {
                $this->numberValidation($att, $ruleArr);
            } else {
                $this->normalValidation($att, $ruleArr);
            }
        }
    }

}