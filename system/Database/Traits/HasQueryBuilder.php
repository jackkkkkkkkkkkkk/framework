<?php


namespace System\Database\Traits;


use System\Database\DBConnection\DBConnection;

trait HasQueryBuilder
{

    private $sql = '';
    private $where = [];
    private $orderBy = [];
    private $limit = [];
    private $values = [];
    private $bindValues = [];

    protected function getSql()
    {
        return $this->sql;
    }

    protected function setSql($query)
    {
        $this->sql = $query;
    }

    protected function resetSql()
    {
        $this->sql = '';
    }

    protected function setWhere($operator, $condition)
    {
        $array = ['operator' => $operator, 'condition' => $condition];
        array_push($this->where, $array);
    }

    protected function resetWhere()
    {
        $this->where = [];
    }

    protected function setOrderBy($name, $expression)
    {
        array_push($this->orderBy, $this->getAttributeName($name) . ' ' . $expression);
    }

    protected function resetOrderBy()
    {
        $this->orderBy = [];
    }

    protected function setLimit($from, $number)
    {
        $this->limit['from'] = $from;
        $this->limit['number'] = $number;
    }

    protected function resetLimit()
    {
        $this->limit = [];
    }

    protected function addValue($attribute, $value)
    {
        $this->bindValues[$attribute] = $value;
        array_push($this->values, $value);
    }

    protected function resetValues()
    {
        $this->values = [];
        $this->bindValues = [];
    }

    protected function reset()
    {
        $this->resetLimit();
        $this->resetOrderBy();
        $this->resetSql();
        $this->resetValues();
        $this->resetWhere();
    }

    protected function executeQuery()
    {
        $query = $this->sql;
        if (!empty($this->where)) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString == '' ? $whereString .= ' ' . $where['condition'] : $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $whereString = ' WHERE ' . $whereString;
            $query .= $whereString;
        }
        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
        if (!empty($this->limit)) {
            $query .= ' LIMIT ' . $this->limit['from'] . ', ' . $this->limit['number'];
        }
        $query .= ';';
        $db = DBConnection::getInstance();
        $stmt = $db->prepare($query);
        if (count($this->values) === count($this->bindValues)) {
            if (count($this->bindValues)) {
                $stmt->execute(array_values($this->bindValues));
            } else {
                $stmt->execute();
            }
        } else {
            $stmt->execute($this->values);
        }
        return $stmt;
    }

    protected function getCount()
    {
        $query = "SELECT COUNT(*) FROM " . $this->getTableName();
        if (!empty($this->where)) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString == '' ? $whereString .= ' ' . $where['condition'] : $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $whereString = ' where ' . $whereString;
            $query .= $whereString;
        }
        $query .= ';';
        $db = DBConnection::getInstance();
        $stmt = $db->prepare($query);
        if (count($this->values) > count($this->bindValues)) {
            $stmt->execute($this->values);
        } else {
            if (count($this->bindValues)) {
                $stmt->execute(array_values($this->bindValues));
            } else {
                $stmt->execute();
            }
        }
        return $stmt->fetchColumn();
    }

    protected function getTableName()
    {
        return '`' . $this->tableName . '`';
    }

    protected function getAttributeName($attribute)
    {
        return '`' . $this->tableName . '`.' . '`' . $attribute . '`';
    }
}