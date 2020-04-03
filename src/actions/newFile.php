<?php


namespace FragmencyScript;


use Fragmency\Files\Files;
use FragmencyScript\ScriptOutput as SO;

trait newFile
{
    protected function newFile ($name = null, $args){
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $path = getcwd().'\\'.$name;
        $return = Files::create($path,join(' ',$args));
        if($return === false) echo SO::color("File already exist",'yellow');
        elseif($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else echo SO::color("File \"$name\" created",'green');
    }

}