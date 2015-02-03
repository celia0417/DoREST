<?php

class Database_DatabaseFactory {

    private static $DBInstance;

    /**
     * 
     * @return Database_DatabaseController
     */
    public static function getInstance() {
        if (self::$DBInstance == Null) {
            self::$DBInstance = new Database_DatabaseController();
        }
        return self::$DBInstance;
    }

}

