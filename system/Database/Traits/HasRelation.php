<?php


namespace System\Database\Traits;


trait HasRelation
{
    protected function hasOne($model, $foreignKey, $localKey)
    {
        if ($this->{$this->primaryKey}) {
            $object = new $model();
            return $object->getHasOneRelation($this->tableName, $foreignKey, $localKey, $this->{$this->primaryKey});
        }
    }

    protected function getHasOneRelation($table, $foreignKey, $otherKey, $otherKeyValue)
    {
        $this->setSql("SELECT `a`.* FROM `$this->tableName` as `a` JOIN `$table` as `b` ON `a`.$foreignKey=`b`.$otherKey");
        $this->setWhere('AND', "`b`.$otherKey=?");
        $this->addValue($otherKey, $otherKeyValue);
        $this->tableName = 'a';
        $stmt = $this->executeQuery();
        $data = $stmt->fetch();
        return $data ? $this->arrayToAttributes($data) : [];
    }

    protected function hasMany($model, $foreignKey, $localKey)
    {
        if ($this->{$this->primaryKey}) {
            $object = new $model();
            return $object->getHasManyRelation($this->tableName, $foreignKey, $localKey, $this->{$this->primaryKey});
        }
    }

    protected function getHasManyRelation($table, $foreignKey, $otherKey, $otherKeyValue)
    {
        $this->setSql("SELECT `a`.* FROM `$this->tableName` as `a` JOIN `$table` as `b` ON `a`.$foreignKey=`b`.$otherKey");
        $this->setWhere('AND', "`b`.$otherKey=?");
        $this->addValue($otherKey, $otherKeyValue);
        $this->tableName = 'a';
        return $this;
    }

    protected function belongsTo($model, $foreignKey, $localKey)
    {
        if ($this->{$this->primaryKey}) {
            $object = new $model();
            return $object->getBelongsToRelation($this->tableName, $foreignKey, $localKey, $this->{$this->primaryKey});
        }
    }

    protected function getBelongsToRelation($table, $foreignKey, $localKey, $localKeyValue)
    {
        $this->setSql("SELECT `a`.* FROM `$this->tableName` as `a` JOIN `$table` as `b` ON `a`.$localKey=`b`.$foreignKey");
        $this->setWhere('AND', "`b`.$localKey=?");
        $this->addValue($localKey, $localKeyValue);
        $this->tableName = 'a';
        $stmt = $this->executeQuery();
        $data = $stmt->fetch();
        return $data ? $this->arrayToAttributes($data) : [];
    }


    protected function belongsToMany($model, $middleTable, $localKey, $foreignKey, $relationKey)
    {
        if ($this->{$this->primaryKey}) {
            $object = new $model();
            return $object->getBelongsToManyRelation($this->tableName, $middleTable, $localKey, $this->$localKey, $foreignKey, $relationKey);
        }
    }

    protected function getBelongsToManyRelation($table, $middleTable, $localKey, $localKeyValue, $foreignKey, $relationKey)
    {
        $this->setSql("SELECT `c`.* FROM (SELECT `m`.* FROM `$middleTable` as `m` WHERE `m`.`$foreignKey`=? ) as `relation` JOIN `$this->tableName` AS `c` ON `c`.`$this->primaryKey`=`relation`.`$relationKey`");
        $this->addValue("`$table`_`$localKey`", $localKeyValue);
        $this->tableName = 'c';
        return $this;
    }
}