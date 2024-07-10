<?php
    require_once "./vendor/autoload.php";

    global $DBA;
    $DBA = new \Tina4\DataMySQL($username = $_ENV["DB_USERAME"], $password = $_ENV["DB_PASSWORD"]);

    $config = new \Tina4\Config(static function (\Tina4\Config $config) {

    });

