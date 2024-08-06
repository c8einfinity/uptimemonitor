<?php
class Tenant extends \Tina4\ORM
{
    public $tableName="tenant";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","tenantName" => "tenant_name","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $tenantName;
	public $createdAt;
	public $updatedAt;

    public $requiredFields = array(
        "tenantName"
    );

    public $virtualFields = ["requiredFields"];
}