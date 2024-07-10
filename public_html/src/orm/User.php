<?php

/**
 * User table for storing user information - Will be a module later
 */
class User extends \Tina4\ORM
{
    public $tableName = "user";
    public $primaryKey = "id";
    public $genPrimaryKey = true;
    public $sotfDelete = true;

    public $id;
    public $username;
    public $password;
    public $email;
    public $created_at;
    public $updated_at;
}