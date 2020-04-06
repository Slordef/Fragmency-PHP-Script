<?php


namespace Fragmency\FragmencyScript;


use Fragmency\Files\Files;
use FragmencyScript\ScriptOutput as SO;

trait newMigration
{
    protected function newMigration ($name) {
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $name = join('',array_map(function ($a){return ucfirst($a);},explode('_',$name)));
        $filename = (new \DateTime())->format('YmdHis').''.(join('_',array_map(function ($a){return strtolower($a);},preg_split('/(?=[A-Z])/',$name)))).'.php';
        $classname = (join('',array_map(function ($a){return ucfirst(strtolower($a));},preg_split('/(?=[A-Z])/',$name))));
        // echo "Attempt to create Migration with name : $name and file name : $filename";
        $folder = $this->rootfolder.'/database/migrations/';
        if(!file_exists($folder)) mkdir($folder,0777,true);
        $path = $folder.$filename;
        $content = Files::read(__DIR__.'/../template/Migration.txt');
        $content = str_replace("<%NAME%>",$classname,$content);
        $return = Files::create($path,$content);
        if($return === false) echo SO::color("File already exist",'yellow');
        elseif($return === "noFolder") echo SO::color("No folder for this",'yellow');
        else echo SO::color("File \"$name\" created",'green');
    }
}