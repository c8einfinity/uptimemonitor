<?php
class Tina4Migration extends \Tina4\ORM
{
    public $tableName="tina4_migration";
    public $primaryKey="migrationId"; //set for primary key
    //public $fieldMapping = ["migrationId" => "migration_id","description" => "description","content" => "content","passed" => "passed"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $migrationId;
	public $description;
	public $content;
	public $passed;
}