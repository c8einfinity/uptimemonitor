<?php
class Notification extends \Tina4\ORM
{
    public $tableName="notification";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","serverId" => "server_id","emailAlert" => "email_alert","emailAddress" => "email_address","threshold" => "threshold","active" => "active","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $serverId;
	public $tenantId;
	public $notificationTypeId;
	public $emailAlert;
	public $emailAddress;
	public $slackUrl;
	public $threshold;
	public $active;
	public $createdAt;
	public $updatedAt;

	public $requiredFields = array(
		"emailAlert",
		"emailAddress",
		"active"
	);

	public $virtualFields = ["requiredFields"];
}