<?php

namespace services;

/**
 * @description Tenants Service. This is used to test all servers for a specific tenant
 */
class TenantService {

    private int $tenantId;
    
    public function __construct(int $tenantId) {
        $this->tenantId = $tenantId;
    }

    /**
     * Test all the monitors for a tenant or server
     */
    public function testMonitors($serverId = 0) {
        if ($serverId > 0)
            $servers = $this->getServers($serverId);
        else $servers = $this->getServers();

        /**
         * Iterate through all the servers and get the monitors
         */
        foreach ($servers as $server) {
            //Test the Port (Sockets)
            //$this->testPorts($server["serverName"], $server["id"], $server["ipAddress"]);

            //Test the HTTP Monitors
            $this->testHttp($server["serverName"], $server["id"], $server["ipAddress"]);
        }
    }

    /**
     * Get all servers or a single server for a tenant
     */
    private function getServers($serverId = 0) {
        $server = new \Server();

        //TODO: Filter on server as well
        $where = "tenant_id = {$this->tenantId}";

        //If we are only updating one server
        $where = $serverId > 0 ? $where . " and id = {$serverId}" : $where;

        return $server->select("id, server_name, ip_address")
            ->where($where)
            ->asArray();
    }

    /**
     * Get the monitors to test for socket connections (Ports)
     */
    private function getSocketMonitors(int $serverId) {
        $serverMonitor = new \ServerMonitor();
        return $serverMonitor->select("id, monitor_type_id, port, next_run")
            ->where("server_id = ? and monitor_type_id = 5 and active = 1", [$serverId]) //TODO: Add MonitorType to Constants
            ->asArray();
    }

    /**
     * Get the monitors to test for HTTP connections
     */
    private function getHttpMonitors(int $serverId) {
        $serverMonitor = new \ServerMonitor();
        return $serverMonitor->select("id, monitor_type_id, url, next_run")
            ->where("server_id = ? and monitor_type_id = 1 and active = 1", [$serverId])
            ->asArray();
    }

    /**
     * Update the monitor info for quick reference on the dashboard an monitor view
     */
    private function updateMonitorInfo($monitorId, $statusCode, $rawResult, $responseTime = 0) {
        $serverMonitor = (new \ServerMonitor());
        if ($serverMonitor->load("id = ?", [$monitorId])) {
            $serverMonitor->lastRun = date("Y-m-d H:i:s");
            
            //Work out the next run
            $serverMonitor->nextRun = date("Y-m-d H:i:s", strtotime($serverMonitor->lastRun . " + {$serverMonitor->interval} minutes"));

            if ($statusCode == HTTP_OK) {
                $status = "Online";
            } else {
                $status = "Error";
            }
            $serverMonitor->lastResult = $statusCode;
            $serverMonitor->lastStatusCode = $statusCode;
            $serverMonitor->status = $status;
            $serverMonitor->lastRunRawResult = json_encode($rawResult);
            $serverMonitor->lastResponseTime = $responseTime;
            $serverMonitor->save();
        }
    }

    /**
     * Port (Socket Connection) Tester
     */
    private function testPorts($serverName, $serverId, $ipAddress) {
        //Get the monitors where we need to test sockets (ports)
        $socketMonitors = $this->getSocketMonitors($serverId);

        $ports = array();

        foreach ($socketMonitors as $socketMonitor) {
            //See if we need to run it
            if ($socketMonitor["nextRun"] > date("Y-m-d H:i:s") && !empty($socketMonitor["nextRun"])) {
                continue;
            }
            
            array_push($ports, array(
                "monitorId" => $socketMonitor["id"],
                "port" => $socketMonitor["port"]
            ));
        }

        $portTester = new \helpers\PortTester($ipAddress, $ports);

        $results =$portTester->check();

        foreach ($results as $result) {
            //TODO: Add MonitorType to Constants
            $monitorType = 5;
            $statusCode = $result['status'] ? HTTP_OK : HTTP_INTERNAL_SERVER_ERROR;

            if (!LogService::logResponse($serverId, $result["monitorId"], $monitorType, $statusCode, $result)) {
                \Tina4\Debug::message("Failed to log response. See previous debug message.");
            }
            else {
                $this->updateMonitorInfo($result["monitorId"], $statusCode, $result);

                //Send to Slack - TODO: Read from config and see if we need to send to Slack
                if ($statusCode != HTTP_OK) {
                    $slackMessage = "SERVER ERROR: Server: {$serverName} IP: {$ipAddress} Port: {$result["port"]} Status: {$statusCode}";
                    $slackHelper = new \helpers\SlackHelper();
                    $slackHelper->postMessage($slackMessage);
                }
            }
        }
    }

    /**
     * HTTP Tester - For specific Urls
     */
    private function testHttp($serverName, $serverId, $ipAddress) {
        $httpMonitors = $this->getHttpMonitors($serverId);
        $monitorType = 1;

        foreach ($httpMonitors as $httpMonitor) {
            //See if we need to run it
            if ($httpMonitor["nextRun"] > date("Y-m-d H:i:s") && !empty($httpMonitor["nextRun"])) {
                continue;
            }

            $httpTester = new \helpers\HttpTester($httpMonitor["url"]);

            $result = $httpTester->testUrl();

            if (!LogService::logResponse($serverId, $httpMonitor["id"], $monitorType, $result['status'], '', $result['time'])) {
                \Tina4\Debug::message("Failed to log response. See previous debug message.");
            }
            else {
                $this->updateMonitorInfo($httpMonitor["id"], $result['status'], '', $result['time']);

                //Send to Slack - TODO: Read from config and see if we need to send to Slack
                if ($result['status'] != HTTP_OK) {
                    $slackMessage = "SERVER ERROR: Server: {$serverName} IP: {$ipAddress} URL: {$httpMonitor["url"]} Status: {$result['status']}";
                    $slackHelper = new \helpers\SlackHelper();
                    $slackHelper->postMessage($slackMessage);
                }
            }
        }
    }
}