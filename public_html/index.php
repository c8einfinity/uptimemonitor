<?php
require_once "./vendor/autoload.php";

global $DBA;
$DBA = new \Tina4\DataSQLite3('../database/'.$_ENV['DB_DATABASE'], $username="", $password="");

$config = new \Tina4\Config(static function (\Tina4\Config $config){
  //Your own config initializations 
});

/*$helper = new helpers\PortTester("127.0.0.1", [7150, 80, 54564]);
print_r ($helper->check());*/

//$tenantService = new services\TenantService(4);

//print_r($tenantService->testMonitors());

echo new \Tina4\Tina4Php($config);

