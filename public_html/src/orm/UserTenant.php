<?php

class UserTenant extends \Tina4\ORM
{
    public $tableName = "user_tenant";
    public $primaryKey = "id";
    public $genPrimaryKey = true;

    public $id;
    public $userId;
    public $tenantId;
    public $created_at;
    public $updated_at;
}