<?php

class Tenant extends \Tina4\ORM
{
    public $tableName = "tenant";
    public $primaryKey = "id";
    public $genPrimaryKey = true;
    public $softDelete = true;

    public $id;
    public $tenantName;
    public $created_at;
    public $updated_at;
}