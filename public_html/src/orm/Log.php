<?php

/**
 * Stores a log of the monitor results
 */
class Log extends \Tina4\ORM
{
    public $tableName = "log";
    public $primaryKey = "id";
    public $genPrimaryKey = true;

    public $id;
    public $tenantId;
    public $siteId;
    public $createdAt;
    public $monitorType;
    public $statusCode;
    public $rawResult;
}