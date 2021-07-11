<?php


namespace System\Database\Traits;


use System\Database\ORM\Model;

trait HasAttributes
{
    protected function registerAttribute(&$object, $attribute, $value)
    {
        $this->inCastsAttributes($attribute) ? $object->$attribute = $this->castDecodeValue($attribute, $value) : $object->$attribute = $value;
    }

    /**
     * @param array $array
     * @param null $object
     * @return Model
     */
    protected function arrayToAttributes(array $array, $object = null)
    {
        if (!$object) {
            $className = get_called_class();
            $object = new $className;
        }
        foreach ($array as $attribute => $value) {
            if ($this->inHiddenAttributes($attribute))
                continue;
            $this->registerAttribute($object, $attribute, $value);
        }
        return $object;
    }

    protected function arrayToObjects(array $array)
    {
        $collection = [];
        foreach ($array as $values) {
            $object = $this->arrayToAttributes($values);
            array_push($collection, $object);
        }
        $this->collection = $collection;
    }

    protected function inCastsAttributes($attribute)
    {
        return in_array($attribute, array_keys($this->casts));
    }

    protected function inHiddenAttributes($attribute)
    {
        return in_array($attribute, $this->hidden);
    }

    protected function castDecodeValue($attribute, $value)
    {
        if ($this->casts[$attribute] == 'object' || $this->casts[$attribute] == 'array') {
            return unserialize($value);
        }
        return $value;
    }

    protected function castEncodeValue($attribute, $value)
    {
        if ($this->casts[$attribute] == 'object' || $this->casts[$attribute] == 'array') {
            return serialize($value);
        }
        return $value;
    }

    protected function arrayToCastEncodeValue(array $array)
    {
        $arr = [];
        foreach ($array as $att => $val) {
            $this->inCastsAttributes($att) ? $arr[$att] = $this->castEncodeValue($att, $val) : $arr[$att] = $val;
        }
        return $arr;
    }
}