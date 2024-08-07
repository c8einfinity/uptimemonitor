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
    public function testTenants() {


    }
}