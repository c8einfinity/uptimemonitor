<?php
use Tina4\ORM;

function synchronizeSchema()
{
    ORM::createTable("log", [
        "id" => "INT(10) AUTO_INCREMENT PRIMARY KEY",
    ]);
}




<?php
use Tina4\ORM;

function synchronizeSchema()
{
    // Define the database schema
    ORM::createTable("user", [
        "id" => "INT(11) AUTO_INCREMENT PRIMARY KEY",
        "name" => "VARCHAR(255) NOT NULL",
        "email" => "VARCHAR(255) NOT NULL",
        "password" => "VARCHAR(255) NOT NULL",
        "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ]);

    // You can add more tables here as needed
}