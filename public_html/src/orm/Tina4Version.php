<?php
class Tina4Version extends \Tina4\ORM
{
    public $tableName="tina4_version";
    public $primaryKey="version"; //set for primary key
    //public $fieldMapping = ["version" => "version","software" => "software","notes" => "notes"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $version;
	public $software;
	public $notes;
}