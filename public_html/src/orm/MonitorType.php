<?php
class MonitorType extends \Tina4\ORM
{
    public $tableName="monitor_type";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","monitorType" => "monitor_type"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $monitorType;

    public $requiredFields = array(
        "monitorType"
    );

    public $virtualFields = ["requiredFields"];
}