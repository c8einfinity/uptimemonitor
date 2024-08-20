<?php
require_once "./vendor/autoload.php";

global $DBA;
$DBA = new \Tina4\DataSQLite3('../database/'.$_ENV['DB_DATABASE'], $username="", $password="");

$config = new \Tina4\Config(static function (\Tina4\Config $config){
  //Your own config initializations 
});

/*$helper = new helpers\PortTester("127.0.0.1", [7150, 80, 54564]);
print_r ($helper->check());*/

/*$tenantService = new services\TenantService(10);

$tenantService->testMonitors();*/

/*$email = new \helpers\MessengerHelper();

$email->sendMail(["name" => "Philip", "email" => "philip.malan@gmail.com"], "Test", "This is a test email");*/

//print_r($email);

//echo $_SESSION['userid'];

//$monitoringService = new services\MonitoringService();

//$monitoringService->testTenants();

echo new \Tina4\Tina4Php($config);

