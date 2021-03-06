<?php


namespace Fragmency\FragmencyScript\Actions;


use Fragmency\Files\Files;
use Fragmency\FragmencyScript\ScriptOutput as SO;

trait newController
{
    protected function newController ($name) {
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $name = ucfirst($name).'Controller';
        $folder = $this->rootfolder.getenv('APP_FOLDER').getenv('CONTROLLERS_FOLDER');
        $path = $folder.'/'.$name.'.php';
        $content = Files::read(__DIR__.'/../template/Controller.txt');
        $content = str_replace("<%NAME%>",$name,$content);
        $return = Files::create($path,$content);
        if($return === false) echo SO::color("File already exist",'yellow');
        elseif($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else echo SO::color("Controller \"$name\" created",'green');
    }

}