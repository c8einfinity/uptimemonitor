<?php

namespace services;

class LogService {

    /**
     * Function to log the response of a monitor
     */
    public static function logResponse($serverId, $serverName, $monitorType, $statusCode, $rawResult, $responseTime = 0) {
        \Tina4\Debug::message("Logging Response - Server: {$serverName}, MonitorType: {$monitorType} , StatusCode: {$statusCode}");
        
        $log = new \Log();

        $log->serverName = $serverName;
        $log->monitorType = $monitorType;
        $log->statusCode = $statusCode;
        $log->responseTime = $responseTime;
        $log->serverId = $serverId;
        
        $log->rawResult = $rawResult;

        if ($log->save())
            return true;
        else return false;
    }

}