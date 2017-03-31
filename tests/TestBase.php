<?php
require_once("ConfigHelper.php");
if (!class_exists('\PHPUnit_Framework_TestCase')) {
     class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}
use DevExtreme\DbSet;

class TestBase extends PHPUnit_Framework_TestCase {
    protected static $mySQL;
    protected static $tableName;
    protected $dbSet;
    public static function setUpBeforeClass() {
        $dbConfig = ConfigHelper::GetConfiguration();
        self::$tableName = $dbConfig["tableName"];
        self::$mySQL = new mysqli($dbConfig["serverName"], $dbConfig["user"], $dbConfig["passowrd"], $dbConfig["databaseName"]);
    }
    public static function tearDownAfterClass() {
        self::$mySQL->close();
    }
    protected function setUp() {
        $this->dbSet = new DbSet(self::$mySQL, self::$tableName);
    }
}
