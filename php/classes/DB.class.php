<?php

class DB
{
    private string $host = "";
    private string $dbName = "";
    private string $usernameDB = "";
    private string $passwordDB = "";
    private static $db = null;
    private PDO $dbHandle;


    private function __construct()
    {
        try {
            $this->dbHandle = new PDO("mysql:host=$this->host;dbname=$this->dbName", $this->usernameDB, $this->passwordDB);
        } catch
        (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            echo phpinfo();
        }
    }

    public static function getDBConnection() {
        if (DB::$db==null) {
            DB::$db = new self();
        }
        return DB::$db->dbHandle;
    }

    public static function closeDBConnection(){
        if (DB::$db!=null){
            DB::$db = null;
        }
    }
}