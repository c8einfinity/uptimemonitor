<?php

/**
 * Lookup for monitor types
 */
class MonitorType extends \Tina4\ORM
{
    public $tableName = "monitor_type";
    public $primaryKey = "id";
    public $genPrimaryKey = true;

    public $id;
    public $monitorType;
}