<?php


namespace FragmencyScript;


use Fragmency\Files\Files;
use FragmencyScript\ScriptOutput as SO;

trait copyFile
{
    protected function copyFile ($name = null,$args){
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

}