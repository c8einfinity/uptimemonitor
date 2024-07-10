<?php

/**
 * Site table for storing site information
 */
class Site extends \Tina4\ORM
{
    public $tableName = "site";
    public $primaryKey = "id";
    public $genPrimaryKey = true;
    public $sotfDelete = true;

    public $id;
    public $tenantId;
    public $siteName;
    public $siteAddress;
    public $created_at;
    public $updated_at;
}