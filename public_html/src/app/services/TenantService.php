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
     * Test all the monitors for a tenant
     */
    public function testMonitors() {
        $servers = $this->getServers();

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

                if (!LogService::logResponse($server->id, $result["monitorId"], $monitorType, $statusCode, $result)) {
                    \Tina4\Debug::message("Failed to log response. See previous debug message.");
                }
                else {
                    //TODO: Add The last result to the server monitor table for quick reference
                }
            }
        }
    }

    /**
     * Get all servers for a tenant
     */
    private function getServers() {
        $server = new \Server();
        return $server->select("id, server_name, ip_address")
            ->where("tenant_id = ?", [$this->tenantId])
            ->asArray();
    }

    /**
     * Get the monitors to test for socket connections (Ports)
     */
    private function getSocketMonitors(int $serverId) {
        $serverMonitor = new \ServerMonitor();
        return $serverMonitor->select("id, monitor_type_id, port")
            ->where("server_id = ? and monitor_type_id = 5", [$serverId])
            ->asArray();
    }

}