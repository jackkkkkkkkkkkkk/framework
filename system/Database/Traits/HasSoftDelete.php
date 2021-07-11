<?php


namespace System\Database\Traits;


trait HasSoftDelete
{
    private $deletedAt = 'deleted_at';

    protected function deleteMethod($id = null)
    {
        $object = $this;
        $this->reset();
        if ($id) {
            $object = $this->findMethod($id);
        }
        if ($object) {
            $object->setSql("UPDATE $object->tableName SET " . $object->getAttributeName($object->deletedAt) . "=now()");
            $object->setWhere('AND', $object->getAttributeName($object->primaryKey) . " = ?");
            $object->addValue($object->primaryKey, $object->{$object->primaryKey});
            return $object->executeQuery();
        }
    }

    protected function findMethod($id)
    {
        $this->reset();
        $this->setSql("SELECT * FROM $this->tableName");
        $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . "= ? ");
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . " IS NULL ");
        $this->addValue($this->primaryKey, $id);
        $stmt = $this->executeQuery();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->setAllowedMethods(['delete', 'update', 'find']);

        return $data ? $this->arrayToAttributes($data) : [];


    }

    protected function allMethod()
    {
        $this->setSql("SELECT * FROM $this->tableName");
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . " IS NULL ");
        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function paginateMethod($perPage)
    {
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . " IS NULL ");
        $totalRows = $this->getCount();
        $totalPages = ceil($totalRows / $perPage);
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $currentRow = ($currentPage - 1) * $perPage;
        $currentPage = max($currentPage, 1);
        $currentPage = min($currentPage, $totalPages);
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
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . " IS NULL ");

        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }
}