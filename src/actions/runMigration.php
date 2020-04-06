<?php


namespace Fragmency\FragmencyScript\Actions;


use Fragmency\Database\MigrationManager;
use Fragmency\Database\Table;

trait runMigration
{
    protected function runMigration (){
        $manager = new MigrationManager();
        Table::setManager($manager);
        $manager->migrate();
    }

}