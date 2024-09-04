<?php
class UserTeam extends \Tina4\ORM
{
    public $tableName="user_team";
    public $primaryKey="id"; //set for primary key
    //public $fieldMapping = ["id" => "id","userId" => "user_id","tenantId" => "tenant_id","createdAt" => "created_at","updatedAt" => "updated_at"];
    //public $genPrimaryKey=false; //set to true if you want to set the primary key
    //public $ignoreFields = []; //fields to ignore in CRUD
    //public $softDelete=true; //uncomment for soft deletes in crud 
    
	public $id;
	public $userId;
	public $teamId;
	public $createdAt;
	public $updatedAt;

    public $requiredFields = array(
        "userId",
        "teamId"
    );

    public $virtualFields = ["requiredFields"];
}