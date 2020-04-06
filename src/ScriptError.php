<?php


namespace Fragmency\FragmencyScript;


use FragmencyScript\ScriptOutput as SO;

class ScriptError extends \Exception
{

    public function __construct($code = 0)
    {
        $s = $this->genMessage($code);
        $s = SO::color($s,'red');
        exit($s);
    }

    public function genMessage ($code){
        switch ($code){
            case FRAGMENCY_ERROR_SCRIPT_UNKNOWN: return "Error was found but unknown";
            case FRAGMENCY_ERROR_SCRIPT_NOARGS: return "Arguments are not found";
            case FRAGMENCY_ERROR_SCRIPT_COUNTARGS: return "Arguments Count no sufficient";
            case FRAGMENCY_ERROR_SCRIPT_ACTION_MISSING: return "Arguments Action not found";
            case FRAGMENCY_ERROR_SCRIPT_MODULE_MISSING: return "Arguments Module not found";
            case FRAGMENCY_ERROR_SCRIPT_NAME_MISSING: return "Arguments Name not found";
            case FRAGMENCY_ERROR_SCRIPT_CALLABLE_NOT_EXIST: return "Action not exist";
            case FRAGMENCY_ERROR_SCRIPT_COMMAND_UNKNOWN: return "Command unknown";
            case FRAGMENCY_ERROR_SCRIPT_FILE_NOT_EXIST: return "File not exist";
            case FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_CORE: return "Fragmency Core is needed and not found";
            case FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_DB: return "Fragmency Database is needed and not found";
            case FRAGMENCY_ERROR_SCRIPT_UNCALLABLE_FILES: return "Fragmency Files is needed and not found";
            default: return "Error was found but unknown";
        }
    }
}