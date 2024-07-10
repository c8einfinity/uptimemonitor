<?php

/**
 * Table to store user login sessions
 */
class UserLogin extends \Tina4\ORM
{
    public $tableName = "user_login";
    public $primaryKey = "id";
    public $genPrimaryKey = true;

    public $id;
    public $userId;
    public $dateTime;
    public $ipAddress;
    public $userAgent;
    public $sessionId;
    public $status;
    public $created_at;
    public $updated_at;
}