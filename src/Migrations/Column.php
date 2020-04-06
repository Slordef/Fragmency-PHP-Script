<?php


namespace Fragmency\FragmencyScript;


class Column
{
    private $type;
    private $name;
    private $size;
    private $null;
    private $rename;
    private $delete;

    public function __construct($type,$name, $size)
    {
        $this->size = $size;
        $this->name = strtolower($name);
        $this->type = $this->getType($type);
    }

    private function getType($type){
        $size = $this->size;
        switch ($type){
            case "integer": return "INT($size)";
            case "string": return "VARCHAR($size)";
            case "text": return "TEXT";
            case "mediumtext": return "MEDIUMTEXT";
            case "longtext": return "LONGTEXT";
            case "bigint": return "BIGINT";
            case "float": return "FLOAT";
            case "double": return "DOUBLE";
            case "date": return "DATE";
            case "datetime": return "DATETIME";
            case "time": return "TIME";
            case "timestamp": return "TIMESTAMP";
        }
    }

    public function nullable(bool $null){ $this->null = $null; }
    public function rename($name){ $this->rename = $name; }
    public function delete(){ $this->delete = true; }

    public function __call($name, $arguments)
    { if(is_callable([$this,$name])) return call_user_func_array([$this,$name],$arguments); }

    private function _get_type(){ return $this->type; }
    private function _get_name(){ return $this->name; }
    private function _get_size(){ return $this->size; }
    private function _get_null(){ return $this->null; }
    private function _get_rename(){ return $this->rename; }
    private function _get_delete(){ return $this->delete; }

}