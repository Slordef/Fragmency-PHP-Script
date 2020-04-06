<?php


namespace Fragmency\FragmencyScript\Actions;


use Fragmency\Files\Files;
use FragmencyScript\ScriptOutput as SO;

trait readFile
{
    protected function readFile ($name = null){
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $return = Files::read($name);
        if($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else{
            echo SO::color("File \"$name\" readed",'green').PHP_EOL;
            echo $return;
        }
    }

}