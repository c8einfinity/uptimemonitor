<?php

/**
 * UptimeMonitorService monitors if everything is working properly
 */
class UptimeMonitorService extends \Tina4\Process implements \Tina4\ProcessInterface
{
    public $name = "UptimeService";
    public $lastRunTime = null;

    /**
     * Uptime monitor is always running
     * @return bool
     */
    public function canRun(): bool
    {
        //Schedule ? select from database to see if there is anything to monitor
        return true;
    }

    /**
     * The code that will run when this process can run
     */
    public function run()
    {
        global $DBA;
        $DBA = new \Tina4\DataSQLite3('../database/'.$_ENV['DB_DATABASE'], $username="", $password="");
        //Establish database connection
        //What do we do here ???
        print("Running Service to check monitors");

        //Time checking is built into the monitoring class
        $monitoringService = new services\MonitoringService();
        $monitoringService->testTenants();

        //Unset everything
        unset($monitoringService);
        $DBA->close();
        unset($DBA);
    }

}