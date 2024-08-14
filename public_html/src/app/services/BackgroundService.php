<?php

namespace services;

/**
 * Class BakcgroundService
 * @package services
 * @description This service is used to run background processes - uses Tina4 Services
 */
class BakcgroundService {

    private int $sleepTime = 5; //TODO: Read that from the monitors table
    private int $tenantId;

    public function __construct(int $tenantId) {
        $this->tenantId = $tenantId;
    }

    public function addTenantService() {

    }

}