<?php
require_once("ConfigHelper.php");
if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
use DevExtreme\DbSet;
use PHPUnit\Framework\TestCase;

class TestBase extends TestCase {
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
