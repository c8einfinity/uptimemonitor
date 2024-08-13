<?php

namespace services;

class MonitoringService {

    private $serverMonitor;
    
    public function __construct() {
        $this->serverMonitor = new \ServerMonitor();
    }

    /**
     * @description Test all servers based on a tenant. The idea is that multiple threads can then run for each workspace
     * @tags monitorings
     */
    public function testTenants($tenantid = 0) {
        //Get all the tenants
        $tenant = new \Tenant();

        if ($tenantid > 0)
            $tenants = $tenant->select("id = ?", $tenantid)->asArray();
        else $tenants = $tenant->select()->asArray();

        foreach ($tenants as $tenant) {
            $tenantService = new TenantService($tenant["id"]);
            $tenantService->testMonitors();
        }
    }

    public function testServer($serverId) {
        $serverMonitor = new \ServerMonitor();
        $serverMonitor->select("server_id = ?", $serverId)->asObject();

        //TODO: Test all monitors for a server
        
    }
}