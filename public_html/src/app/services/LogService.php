<?php

namespace services;

class LogService {

    /**
     * Function to log the response of a monitor
     */
    public static function logResponse($serverId, $serverMonitorId, $monitorType, $statusCode, $rawResult, $responseTime = 0) {
        \Tina4\Debug::message("Logging Response - ServerId: {$serverId}, ServerMonitorId: {$serverMonitorId}, MonitorType: {$monitorType} , StatusCode: {$statusCode}");
        
        $log = new \Log();

        $log->serverId = $serverId;
        $log->monitorType = $monitorType;
        $log->serverMonitorId = $serverMonitorId;
        $log->statusCode = $statusCode;
        $log->responseTime = $responseTime;
        
        $log->rawResult = $rawResult;

        if ($log->save())
            return true;
        else return false;
    }

}