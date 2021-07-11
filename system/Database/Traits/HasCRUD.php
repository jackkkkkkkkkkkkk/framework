<?php


namespace System\Database\Traits;


use PDO;
use System\Database\DBConnection\DBConnection;
use System\Database\ORM\Model;

trait HasCRUD
{

    protected function createMethod($values = [])
    {
        if ($values) {
            $values = $this->arrayToCastEncodeValue($values);
            $object = $this->arrayToAttributes($values);
            return $object->saveMethod();
        }
    }

    protected function updateMethod($values = [])
    {
        if ($values) {
            $values = $this->arrayToCastEncodeValue($values);
            $object = $this->arrayToAttributes($values);
            return $object->saveMethod();
        }
    }

    protected function paginateMethod($perPage)
    {
        $totalRows = $this->getCount();
        $totalPages = ceil($totalRows / $perPage);
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $currentPage = max($currentPage, 1);
        $currentPage = min($currentPage, $totalPages);
        $currentRow = ($currentPage - 1) * $perPage;
        $this->setLimit($currentRow, $perPage);
        if ($this->sql == '') {
            $this->setSql("SELECT * FROM " . $this->getTableName());
        }

        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }


    protected function getMethod($array = [])
    {
        if ($this->sql == '') {
            if ($array) {
                foreach ($array as $k => $v) {
                    $array[$k] = $this->getAttributeName($v);
                }
                $fields = implode(', ', $array);
            } else {
                $fields = $this->tableName . '.*';
            }
            $this->setSql("SELECT $fields FROM " . $this->getTableName());
        }
        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function limitMethod($from, $number)
    {
        $this->setLimit($from, $number);
        $this->setAllowedMethods(['paginate', 'get', 'limit']);
    }

    protected function orderByMethod($attribute, $expression)
    {
        $this->setOrderBy($attribute, $expression);
        $this->setAllowedMethods(['paginate', 'get', 'limit']);
    }

    protected function whereInMethod($attribute, $values)
    {
        if (is_array($values)) {
            $bindValues = [];
            foreach ($values as $value) {
                $this->addValue($attribute, $value);
                array_push($bindValues, '?');
            }
            $condition = "IN (" . implode(', ', $bindValues) . ")";
        } else {
            $condition = "IN ( ? )";
            $this->addValue($attribute, $values);
        }
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereIn', 'whereOr', 'whereNotNull', 'whereNot', 'whereNull', 'orderBy', 'paginate', 'get', 'limit']);
        return $this;
    }

    protected function whereMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue) {
            $condition = $this->getAttributeName($attribute) . " $firstValue ? ";
            $this->addValue($attribute, $secondValue);
        } else {
            $condition = $this->getAttributeName($attribute) . " = ? ";
            $this->addValue($attribute, $firstValue);
        }
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereIn', 'whereOr', 'whereNotNull', 'whereNot', 'whereNull', 'orderBy', 'paginate', 'get', 'limit']);
        return $this;
    }

    protected function whereOrMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue) {
            $condition = $this->getAttributeName($attribute) . " $firstValue ? ";
            $this->addValue($attribute, $secondValue);
        } else {
            $condition = $this->getAttributeName($attribute) . " = ? ";
            $this->addValue($attribute, $firstValue);
        }
        $operator = 'OR';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereIn', 'whereOr', 'whereNotNull', 'whereNot', 'whereNull', 'orderBy', 'paginate', 'get', 'limit']);
        return $this;
    }

    protected function whereNullMethod($attribute)
    {
        $condition = $this->getAttributeName($attribute) . " IS NULL ";
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereIn', 'whereOr', 'whereNotNull', 'whereNot', 'whereNull', 'orderBy', 'paginate', 'get', 'limit']);
        return $this;
    }

    protected function whereNotNullMethod($attribute)
    {
        $condition = $this->getAttributeName($attribute) . " IS NOT NULL ";
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereIn', 'whereOr', 'whereNotNull', 'whereNot', 'whereNull', 'orderBy', 'paginate', 'get', 'limit']);
        return $this;
    }

    protected function findMethod($id)
    {
        $this->setSql("SELECT * FROM $this->tableName");
        $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . "= ? ");
        $this->addValue($this->primaryKey, $id);
        $stmt = $this->executeQuery();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->setAllowedMethods(['delete', 'update', 'find']);

        return $data ? $this->arrayToAttributes($data) : [];


    }

    protected function allMethod()
    {
        $this->setSql("SELECT * FROM $this->tableName");
        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function deleteMethod($id = null)
    {
        $object = $this;
        $this->reset();
        if ($id) {
            $object = $this->findMethod($id);
            $this->reset();
        }
        $object->setSql("DELETE FROM $object->tableName");
        $object->setWhere('AND', $object->getAttributeName($object->primaryKey) . " = ?");
        $object->addValue($object->primaryKey, $object->{$object->primaryKey});
        return $object->executeQuery();
    }


    /**
     * @return Model
     */
    protected function saveMethod()
    {
        $fillString = $this->fill();
        if (isset($this->{$this->primaryKey})) {
            $this->setSql("UPDATE $this->tableName SET $fillString , " . $this->getAttributeName($this->updatedAt) . "= now() ");
            $this->addValue($this->primaryKey, $this->{$this->primaryKey});
            $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . "= ?");
        } else {
            $this->setSql("INSERT INTO $this->tableName SET $fillString , " . $this->getAttributeName($this->createdAt) . "= now()");
        }
        $this->executeQuery();
        $this->reset();

        if (!isset($this->{$this->primaryKey})) {
            $object = $this->findMethod(DBConnection::lastInsertId());
            $vars = get_object_vars($object);
            $cVars = get_class_vars(get_called_class());
            $dVars = array_diff(array_keys($vars), array_keys($cVars));

            foreach ($dVars as $attribute) {
                $this->registerAttribute($this, $attribute, $object->$attribute);
            }
        }

        $this->reset();
        $this->setAllowedMethods(['delete', 'update', 'find']);
        return $this;

    }

    protected function fill()
    {
        $fillArray = [];
        foreach ($this->fillable as $attribute) {
            if (isset($this->$attribute)) {
                array_push($fillArray, $this->getAttributeName($attribute) . " =?");
                if ($this->inCastsAttributes($attribute)) {
                    $this->addValue($attribute, $this->castEncodeValue($attribute, $this->$attribute));
                } else {
                    $this->addValue($attribute, $this->$attribute);
                }
            }
        }
        $fillString = implode(', ', $fillArray);
        return $fillString;
    }
}