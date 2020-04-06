<?php


namespace Fragmency\FragmencyScript\Actions;


trait newModel
{
    protected function newModel ($name) {
        if(!isset($name) || $name === null) throw new ScriptError(FRAGMENCY_ERROR_SCRIPT_NAME_MISSING);
        echo "Attempt to create Model with name : $name";
    }

}