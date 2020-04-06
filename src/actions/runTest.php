<?php


namespace Fragmency\FragmencyScript\Actions;


trait runTest
{
    protected function runTest(){
        $path = getcwd().'\\'."testdir\\testindir\\testmore";
        mkdir($path,0777,true);
    }

}