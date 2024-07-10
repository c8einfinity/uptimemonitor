<?php

/**
 * Site Monitor table for storing site monitor information
 */
class SiteMonitor extends \Tina4\ORM
{
    public $tableName = "site_monitor";
    public $primaryKey = "id";
    public $genPrimaryKey = true;
    public $sotfDelete = true;

    public $id;
    public $tenantId;
    public $siteId;
    public $monitorTypeId;
    public $interval;
    public $status;
    public $lastRun;
    public $nextRun;
    public $lastResult;
    public $lastStatusCode;
    public $lastRunTime;
    public $lastRunDuration;
    public $lastRunRawResult;
    public $active;
    public $created_at;
    public $updated_at;
}