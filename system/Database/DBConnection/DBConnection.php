<?php


namespace System\Database\DBConnection;

use PDO;
use PDOException;

class DBConnection
{
    private static $dbInstance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$dbInstance == null) {
            $instance = new DBConnection();
            self::$dbInstance = $instance->dbConnection();
        }
        return self::$dbInstance;
    }

    private function dbConnection()
    {
        $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        try {
            return new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            echo 'db connection error:' . $e->getMessage();
            return;
        }
    }

    public static function lastInsertId()
    {
        return self::getInstance()->lastInsertId();
    }

}