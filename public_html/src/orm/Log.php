<?php
/**
 * @OA\Schema(
 *  schema="Log",
 * required={"id", "siteId", "createdAt", "monitorType", "statusCode", "rawResult"},
 * title="Log",
 * description="Log to store all the monitoring results. The id, siteId and createdAt will be added automatically."
 * )
 */

class Log extends \Tina4\ORM
{
    public $primaryKey = "id";
    public $genPrimaryKey = true;

    /**
     * @var integer default 0 not null
     */
    public $id;

    /**
     * @var integer default 0 not null
     */
    public $siteId;

    /**
     * @var timestamp default current_timestamp
     */
    public $createdAt;

    /**
     * @var integer default 0 not null
     */
    public $monitorType;

    /**
     * @var varchar(100)
     */
    public $statusCode;

    /**
     * @var varchar(255)
     */
    public $rawResult;
}