<?php


namespace FragmencyScript;

use Dotenv\Dotenv;
use Fragmency\Core\Application;
use Fragmency\Database\MigrationManager;
use Fragmency\Database\Table;
use Fragmency\Files\Files;
use FragmencyScript\ScriptOutput as SO;

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
    
    private function newController ($name) {
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $name = ucfirst($name).'Controller';
        echo "Attempt to create Controller with name : $name";
    }
    private function newMigration ($name) {
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $name = join('',array_map(function ($a){return ucfirst($a);},explode('_',$name)));
        $filename = (new \DateTime())->format('YmdHis').''.(join('_',array_map(function ($a){return strtolower($a);},preg_split('/(?=[A-Z])/',$name)))).'.php';
        $classname = (join('',array_map(function ($a){return ucfirst(strtolower($a));},preg_split('/(?=[A-Z])/',$name))));
        // echo "Attempt to create Migration with name : $name and file name : $filename";
        $folder = $this->rootfolder.'/database/migrations/';
        if(!file_exists($folder)) mkdir($folder,0777,true);
        $path = $folder.$filename;
        $content = Files::read(__DIR__.'/Migrations/template/Migration.txt');
        $content = str_replace("<%NAME%>",$classname,$content);
        $return = Files::create($path,$content);
        if($return === false) echo SO::color("File already exist",'yellow');
        elseif($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else echo SO::color("File \"$name\" created",'green');
    }
    private function newModel ($name) {
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        echo "Attempt to create Model with name : $name";
    }

    private function runMigration (){
        $manager = new MigrationManager();
        Table::setManager($manager);
        $manager->migrate();
    }

    private function testDb (){
        $query = ScriptDB::getDB();
        foreach ($query as $q){
            echo $q . PHP_EOL;
        }
    }

    private function readFile ($name = null){
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $return = Files::read($name);
        if($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else{
            echo SO::color("File \"$name\" readed",'green').PHP_EOL;
            echo $return;
        }
    }
    private function newFile ($name = null, $args){
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $path = getcwd().'\\'.$name;
        $return = Files::create($path,join(' ',$args));
        if($return === false) echo SO::color("File already exist",'yellow');
        elseif($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else echo SO::color("File \"$name\" created",'green');
    }
    private function copyFile ($name = null,$args){
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        if(!isset($args[0]) || $args[0] === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $path = getcwd().'\\'.$name;
        $path2 = getcwd().'\\'.$args[0];
        $content = Files::read($path);
        if($content === false) {echo SO::color("Can't find $path",'red'); exit;}
        elseif($content === "noFolder") {echo SO::color("No folder for this",'yellow'); exit;}
        $return = Files::create($path2,$content);
        if($return === false) echo SO::color("File already exist",'yellow');
        elseif($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else echo SO::color("File \"$args[0]\" created",'green');
    }
    private function rmFile ($name = null){
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $path = getcwd().'\\'.$name;
        if(!file_exists($path)) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_FILE_NOT_EXIST);
        echo SO::color("You are about to delete file \"$name\", confirm (y/N) : ", 'yellow');
        $line = readline("");
        if(strtolower(trim($line)) != 'yes' && strtolower(trim($line)) != 'y'){
            echo PHP_EOL.SO::color("ABORTING!",'red');
            exit;
        }
        $return = Files::remove($path);
        echo SO::color("File \"$name\" deleted",'green');
    }

    private function runTest(){
        $path = getcwd().'\\'."testdir\\testindir\\testmore";
        mkdir($path,0777,true);
    }
}