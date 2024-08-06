<?php
class ServerMonitor extends \Tina4\ORM
{
    public $tableName="server_monitor";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","serverId" => "server_id","monitorTypeId" => "monitor_type_id","ipAddress" => "ip_address","domain" => "domain","port" => "port","status" => "status","lastRun" => "last_run","nextRun" => "next_run","lastResult" => "last_result","lastStatusCode" => "last_status_code","lastRunDuration" => "last_run_duration","lastRunRawResult" => "last_run_raw_result","active" => "active","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $serverId;
	public $monitorTypeId;
	public $ipAddress;
	public $domain;
	public $port;
	public $status;
	public $lastRun;
	public $nextRun;
	public $lastResult;
	public $lastStatusCode;
	public $lastRunDuration;
	public $lastRunRawResult;
	public $active;
	public $createdAt;
	public $updatedAt;

	public $requiredFields = array(
		"serverId",
		"monitorTypeId",
		"port",
		"active"
	);

	public $virtualFields = ["requiredFields"];
}