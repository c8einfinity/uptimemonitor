<?php


\Tina4\Get::add("/system/start-service", function (\Tina4\Response $response){
    $service = new \Tina4\Service();
    $service->removeProcess("main-service");
    $service->addProcess(new UptimeMonitorService("main-service"));
    return $response ("OK");
});