<?php


namespace FragmencyScript;


use Fragmency\Files\Files;
use FragmencyScript\ScriptOutput as SO;

trait rmFile
{
    protected function rmFile ($name = null){
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

}