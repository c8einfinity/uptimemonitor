<?php
class UserRole extends \Tina4\ORM
{
    public $tableName="user_role";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","username" => "username","password" => "password","email" => "email","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $userId;
    public $userRoleId;
	public $createdAt;
	public $updatedAt;

    public $requiredFields = array(
        "userId",
        "userRoleId"
    );

    public $virtualFields = ["requiredFields"];
}