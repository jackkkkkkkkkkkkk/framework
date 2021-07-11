<?php


namespace System\Database\DBBuilder;


use System\Database\DBConnection\DBConnection;

class DBBuilder
{
    public function __construct()
    {
        $this->createTables();
        die('tables created successfully');
    }

    private function getMigrations()
    {
        $migrationsPath = BASE_DIR . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        $allMigrations = glob($migrationsPath . '*.php');
        $oldMigrations = $this->getOldMigrations();
        $newMigrations = array_diff($allMigrations, $oldMigrations);
        $sqlArrays = [];
        foreach ($newMigrations as $migration) {
            $sqlArray = require $migration;
            array_push($sqlArrays, $sqlArray[0]);
        }
        $this->putOldMigrations($allMigrations);
        return $sqlArrays;

    }

    private function getOldMigrations()
    {
        $file = file_get_contents(__DIR__ . '/oldTables.db');
        return empty($file) ? [] : unserialize($file);

    }

    private function putOldMigrations($array)
    {
        file_put_contents(__DIR__ . 'oldTables.db', serialize($array));
    }

    private function createTables()
    {
        $migrations = $this->getMigrations();
        $db = DBConnection::getInstance();
        foreach ($migrations as $migration) {
            $stmt = $db->prepare($migration);
            $stmt->execute();
        }
        return true;
    }
}