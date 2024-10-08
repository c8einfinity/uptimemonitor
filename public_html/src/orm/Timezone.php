<?php
class Timezone extends \Tina4\ORM
{
    public $tableName="timezone";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","tenantId" => "tenant_id","serverName" => "server_name","serverDescription" => "server_description","active" => "active","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $timezone;
	public $createdAt;
	public $updatedAt;

	public $requiredFields = array(
		"timezone"
	);

	public $virtualFields = ["requiredFields"];
}