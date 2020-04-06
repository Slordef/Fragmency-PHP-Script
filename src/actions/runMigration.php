<?php


namespace Fragmency\FragmencyScript\Actions;


use Fragmency\FragmencyScript\Migrations\MigrationManager;
use Fragmency\FragmencyScript\Migrations\Table;

trait runMigration
{
    protected function runMigration (){
        $manager = new MigrationManager();
        Table::setManager($manager);
        $manager->migrate();
    }

}