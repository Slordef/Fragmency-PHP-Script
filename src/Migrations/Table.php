<?php


namespace Fragmency\Database;


/**
 * Class Table
 * @package Fragmency\Database
 */
class Table
{
    /**
     * Use Table name (Create if not exist | modify if exist)
     * On Create, column `id` is automaticaly create
     * @param string $name Name of Table
     * @param callable $callable function(Table $table){}
     */
    public static function use(string $name, callable $callable){
        $table = new self($name,$callable);
        self::$manager->addTable($table);
    }

    private $tableName;
    private $delete = false;
    private $rename;
    private $columns = [];
    private static $manager;
    /**
     * Table constructor, use static function for this
     * @param $request
     * @param $name
     * @param $callable
     */
    private function __construct($name, $callable){
        $this->tableName = strtolower($name);
        $callable($this);
    }

    /**
     * Rename current Table
     * @param $name
     * @return $this
     */
    public function renameTable($name){ $this->rename = strtolower($name); return $this; }

    /**
     * Delete current Table
     */
    public function deleteTable(){ $this->delete = true; }

    public function integer($name,$size = 11){ $col = new Column("integer",$name,$size); $this->columns[] = $col; return $col; }
    public function string($name,$size = 256){ $col = new Column("string",$name,$size); $this->columns[] = $col; return $col; }
    public function text($name){ $col = new Column("text",$name); $this->columns[] = $col; return $col; }
    public function mediumtext($name){ $col = new Column("mediumtext",$name); $this->columns[] = $col; return $col; }
    public function longtext($name){ $col = new Column("longtext",$name); $this->columns[] = $col; return $col; }
    public function bigint($name){ $col = new Column("bigint",$name); $this->columns[] = $col; return $col; }
    public function float($name){ $col = new Column("float",$name); $this->columns[] = $col; return $col; }
    public function double($name){ $col = new Column("double",$name); $this->columns[] = $col; return $col; }
    public function date($name){ $col = new Column("date",$name); $this->columns[] = $col; return $col; }
    public function datetime($name){ $col = new Column("datetime",$name); $this->columns[] = $col; return $col; }
    public function time($name){ $col = new Column("time",$name); $this->columns[] = $col; return $col; }
    public function timestamp($name){ $col = new Column("timestamp",$name); $this->columns[] = $col; return $col; }

    public static function __callStatic($name, $arguments)
    {
        $call = '_S_'.$name;
        if(is_callable([__CLASS__,$call])) return call_user_func_array([__CLASS__,$call],$arguments);
    }

    private static function _S_setManager($manager){
        self::$manager = $manager;
    }

    public function __call($name, $arguments)
    { if(is_callable([$this,$name])) return call_user_func_array([$this,$name],$arguments); }

    private function _get_tableName(){ return $this->tableName; }
    private function _get_delete(){ return $this->delete; }
    private function _get_rename(){ return $this->rename; }
    private function _get_columns(){ return $this->columns; }
}