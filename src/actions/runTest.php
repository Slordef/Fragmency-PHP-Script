<?php


namespace FragmencyScript;


trait runTest
{
    protected function runTest(){
        $path = getcwd().'\\'."testdir\\testindir\\testmore";
        mkdir($path,0777,true);
    }

}