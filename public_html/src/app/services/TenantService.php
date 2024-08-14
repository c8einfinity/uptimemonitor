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
            //Get the monitors where we need to test sockets (ports)
            $socketMonitors = $this->getSocketMonitors($server["id"]);

           $ports = array();

            foreach ($socketMonitors as $socketMonitor) {
                array_push($ports, array(
                    "monitorId" => $socketMonitor["id"],
                    "port" => $socketMonitor["port"]
                ));
            }

            $portTester = new \helpers\PortTester($server["ipAddress"], $ports);

            $results =$portTester->check();

            foreach ($results as $result) {
                //TODO: Add MonitorType to Constants
                $monitorType = 5;
                $statusCode = $result['status'] ? HTTP_OK : HTTP_FORBIDDEN;

                if (!LogService::logResponse($server["id"], $result["monitorId"], $monitorType, $statusCode, $result)) {
                    \Tina4\Debug::message("Failed to log response. See previous debug message.");
                }
                else {
                    $serverMonitor = (new \ServerMonitor());
                    if ($serverMonitor->load("id = ?", [$result["monitorId"]])) {
                        $serverMonitor->lastRun = date("Y-m-d H:i:s");
                        $serverMonitor->lastResult = $statusCode;
                        $serverMonitor->lastStatusCode = $statusCode;
                        $serverMonitor->lastRunRawResult = json_encode($result);
                        $serverMonitor->save();
                    }

                    //Send to Slack - TODO: Read from config and see if we need to send to Slack
                    if ($statusCode != HTTP_OK) {
                        $slackMessage = "SERVER ERROR: Server: {$server["serverName"]} IP: {$server["ipAddress"]} {Port: {$result["port"]} Status: {$statusCode}";
                        $slackHelper = new \helpers\SlackHelper();
                        $slackHelper->postMessage($slackMessage);
                    }
                }
            }
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
        return $serverMonitor->select("id, monitor_type_id, port")
            ->where("server_id = ? and monitor_type_id = 5", [$serverId]) //TODO: Add MonitorType to Constants
            ->asArray();
    }

}