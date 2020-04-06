<?php


namespace Fragmency\FragmencyScript;

use Dotenv\Dotenv;
use Fragmency\Core\Application;
use Fragmency\Files\Files;
use Fragmency\FragmencyScript\ScriptOutput as SO;

const FRAGMENCY_ERROR_SCRIPT_UNKNOWN = 0;
const FRAGMENCY_ERROR_SCRIPT_NOARGS = 1;
const FRAGMENCY_ERROR_SCRIPT_COUNTARGS = 2;
const FRAGMENCY_ERROR_SCRIPT_ACTION_MISSING = 3;
const FRAGMENCY_ERROR_SCRIPT_MODULE_MISSING = 4;
const FRAGMENCY_ERROR_SCRIPT_NAME_MISSING = 5;
const FRAGMENCY_ERROR_SCRIPT_CALLABLE_NOT_EXIST = 6;
const FRAGMENCY_ERROR_SCRIPT_COMMAND_UNKNOWN = 7;
const FRAGMENCY_ERROR_SCRIPT_FILE_NOT_EXIST = 8;
const FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_CORE = 10;
const FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_DB = 11;
const FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_FILES = 12;

class Script
{
    use newMigration,newController,newModel,runMigration;

    private $rootfolder;
    private $configfolder;
    public function __construct(){
        $this->rootfolder = dirname(__FILE__,5);
        if(file_exists($this->rootfolder.'/.env')) {
            $dotenv = Dotenv::createImmutable($this->rootfolder);
            $dotenv->load();
        }
        $args = $_SERVER['argv'];
        $this->checkClass();
        if(!isset($args)) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NOARGS);
        [$action,$module,$name,$oargs] = Parser::gen($args);
        if(isset($action) && isset($module)) {
            $call = strtolower($action) . ucfirst($module);
            if(is_callable([$this,$call])) $this->$call($name,$oargs);
            else throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_CALLABLE_NOT_EXIST);
        }else throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_COMMAND_UNKNOWN);
    }
    
    private function checkClass(){
        if(!class_exists("Fragmency\Core\Fragmency")) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_CORE);
        if(!class_exists("Fragmency\Database\DB")) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_DB);
        if(!class_exists("Fragmency\Files\Files")) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_FILES);
    }
}