<?php
class Log extends \Tina4\ORM
{
    public $tableName="log";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","serverId" => "server_id","createdAt" => "created_at","monitorType" => "monitor_type","statusCode" => "status_code","rawResult" => "raw_result"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
    public $serverId;
    public $serverName;
	public $createdAt;
    public $monitorType;
	public $statusCode;
	public $rawResult;
    public $responseTime;
    public $userid;

    public $requiredFields = array(
        "statusCode",
        "rawResult"
    );

    public $virtualFields = ["requiredFields"];
}