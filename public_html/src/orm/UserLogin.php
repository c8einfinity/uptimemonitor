<?php
class UserLogin extends \Tina4\ORM
{
    public $tableName="user_login";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","dateTime" => "date_time","ipAddress" => "ip_address","userAgent" => "user_agent","sessionId" => "session_id","status" => "status","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $dateTime;
	public $ipAddress;
	public $userAgent;
	public $sessionId;
	public $status;
	public $createdAt;
	public $updatedAt;
}