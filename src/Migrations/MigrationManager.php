<?php


namespace Fragmency\FragmencyScript\Migrations;


use Fragmency\FragmencyScript\ScriptOutput as SO;

class MigrationManager
{
    private $rootfolder;
    private $folder = '/database/migrations';
    private $tables = [];
    private $DBManager;
    private $migrationTable;

    public function __construct(){
        $this->DBManager = new DatabaseManager($this);
        $this->rootfolder = dirname(__DIR__,5);

        $this->checkAndInitMigrationTable();
    }

    private function checkAndInitMigrationTable(){
        $schema = $this->getSchema();
        if(!array_key_exists("migrations",$schema)){
            $query = "CREATE TABLE migrations (`id` INT NOT NULL AUTO_INCREMENT,`migration` VARCHAR(256) NOT NULL, PRIMARY KEY (`id`))";
            $this->DBManager->exec($query);
        }
        $query = "SELECT * FROM migrations";
        $this->migrationTable = $this->DBManager->query($query);
    }

    public function __call($name, $arguments)
    {
        $call = '_'.$name;
        if(is_callable([$this,$call])) return call_user_func_array([$this,$call],$arguments);
    }

    private function _migrate(){
        $scan = scandir($this->rootfolder.'/database/migrations/');
        $scan = array_slice($scan,2);
        foreach ($scan as $migration){
            $name = explode('.',$migration)[0];
            if(array_search($name, array_column($this->migrationTable, 'migration')) !== false) continue;
            require $this->rootfolder.'/database/migrations/'.$migration;
            $className = join('',array_map(function ($a){return ucfirst($a);},array_slice(explode('_',(explode('.',$migration))[0]),1)));
            $class = "Migration\\".$className;
            $instance = new $class();
            if(is_callable([$instance,"migrate"])){
                echo SO::color("Migration $name : open","light_blue").PHP_EOL;
                $instance->migrate();
                $query = $this->compile();
                $query .= "INSERT INTO migrations (`migration`) VALUES ('$name');";
                $this->DBManager->query($query,false);
                echo SO::color("Migration $name : done","green").PHP_EOL;
                $this->resetTables();
                sleep(1);
            }else echo SO::color("Function \"migrate\" not found in migration class \"$className\"",'red').PHP_EOL;
        }
        echo SO::color("All migrations done","green","white").PHP_EOL;
    }

    private function _addTable($table){
        $this->tables[] = $table;
    }
    private function resetTables(){
        $this->tables = [];
    }

    private function getSchema(){
        $db = getenv('DATABASE_USE_DB');
        if(!$db) return false;
        $query = "SELECT
    c.table_schema as SchemaName,
    c.table_name as TableName,
    c.column_name as ColumnName,
    c.data_type as DataType,
    c.IS_NULLABLE as Nullable,
       c.CHARACTER_MAXIMUM_LENGTH as Size
FROM information_schema.columns c
    INNER JOIN information_schema.tables t
        ON c.table_name = t.table_name
               AND c.table_schema = t.table_schema
               AND t.table_type = 'BASE TABLE'
WHERE c.TABLE_SCHEMA = '$db'
ORDER BY SchemaName,
         TableName,
         ordinal_position;";
        $rows = $this->DBManager->query($query);
        $schema = [];
        foreach ($rows as $r){
            if(!isset($schema[$r['TableName']])) $schema[$r['TableName']] = [];
            $schema[$r['TableName']][$r['ColumnName']] = [
                'name' => $r['ColumnName'],
                'type' => $r['DataType'],
                'size' => $r['Size'],
                'null' => $r['Nullable']
            ];
        }
        return $schema;
    }

    private function compile(){
        $schema = $this->getSchema();
        $querys = [];
        foreach ($this->tables as $t){
            $t_name = $t->_get_tableName();
            if($t_name === "migrations") continue;
            if($t->_get_delete()){ $querys[] = "DROP TABLE $t_name ;"; continue; }
            $create = !array_key_exists($t_name,$schema);
            $columns = [];
            foreach ($t->_get_columns() as $c) {
                $column = "";
                $c_type = $c->_get_type();
                $c_name = $c->_get_name();
                $c_null = $c->_get_null() ? "NULL": "NOT NULL";
                $c_rename = $c->_get_rename();
                $c_delete = $c->_get_delete();
                if($create){
                    $column = "`$c_name` $c_type $c_null";
                }elseif($c_delete){
                    $column = "DROP `$c_name`";
                }else{
                    $add = !array_key_exists($c_name,$schema[$t_name]);
                    if($add) $column = "ADD `$c_name` $c_type $c_null";
                    elseif($c_rename) $column = "CHANGE `$c_name` `$c_rename` $c_type $c_null";
                    else $column = "MODIFY `$c_name` $c_type $c_null";
                }
                $columns[] = $column;
            }
            $contruct_columns = join(',',$columns);
            if($create) $queryContruct = "CREATE TABLE $t_name (`id` INT NOT NULL AUTO_INCREMENT, $contruct_columns, PRIMARY KEY (`id`));";
            else $queryContruct = "ALTER TABLE $t_name $contruct_columns;";
            $querys[] = $queryContruct;
        }
        $query = join('',$querys);
        return $query;
    }

}