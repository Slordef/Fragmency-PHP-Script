<?php


namespace FragmencyScript;


class Parser
{
    public static function gen($args){
        // if(count($args) < 3) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_COUNTARGS);
        $args = array_slice($args,1);
        if(count($args) < 1) return ["","",""];
        [$action,$module] = explode(':',$args[0]);
        // if(!isset($action)) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_ACTION_MISSING);
        // if(!isset($module)) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_MODULE_MISSING);
        // if(!isset($args[1])) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        $name = $args[1] ?? null;
        $oargs = array_slice($args,2);
        return [$action,$module,$name,$oargs];
    }
}