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
            //Get the server notifications - Based on tenant
            $notification = new \Notification();
            $notifications = $notification->select("*")
                ->where("tenant_id = ?", [$server["tenantId"]])
                ->asArray();

            //Test the Port (Sockets)
            $this->testPorts($server["serverName"], $server["id"], $server["ipAddress"], $notifications);

            //Test the HTTP Monitors
            $this->testHttp($server["serverName"], $server["id"], $server["ipAddress"], $notifications);
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

        return $server->select("id, server_name, ip_address, tenant_id")
            ->where($where)
            ->asArray();
    }

    /**
     * Get the monitors to test for socket connections (Ports)
     */
    private function getSocketMonitors(int $serverId) {
        $serverMonitor = new \ServerMonitor();
        return $serverMonitor->select("id, monitor_type_id, port, next_run, notification_count")
            ->where("server_id = ? and monitor_type_id = 5 and active = 1", [$serverId]) //TODO: Add MonitorType to Constants
            ->asArray();
    }

    /**
     * Get the monitors to test for HTTP connections
     */
    private function getHttpMonitors(int $serverId) {
        $serverMonitor = new \ServerMonitor();
        return $serverMonitor->select("id, monitor_type_id, url, next_run, notification_count")
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
                $serverMonitor->notificationCount = 0;
            } else {
                $status = "Error";
                $serverMonitor->notificationCount += 1;
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
    private function testPorts($serverName, $serverId, $ipAddress, $notifications) {
        //Get the monitors where we need to test sockets (ports)
        $socketMonitors = $this->getSocketMonitors($serverId);

        $ports = array();

        $sendNotifications = false;

        //If we have notifications configured then send notifications if needed
        if (!empty($notifications))
            $sendNotifications = true;

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
            $monitorType = "PORT"; //TODO Add to Constants or get from MonitorType table
            $statusCode = $result['status'] ? HTTP_OK : HTTP_INTERNAL_SERVER_ERROR;

            if (!LogService::logResponse($serverId, $serverName, $monitorType, $statusCode, $result, $result['time'])) {
                \Tina4\Debug::message("Failed to log response. See previous debug message.");
            }
            else {
                $alert = false;
                $message = "UPTIMEMONITOR.AI\nSERVER: {$serverName}\nIP: {$ipAddress}\nPORT: {$result['port']}\nSTATUS: {$statusCode}\nSERVER TIME: " . date('Y-m-d H:i:s') . "\nRESPONSE TIME: {$result['time']}";

                //Get the previous monitor info to see if need to send a slack message if the server is online again
                $previousStatus = $this->getPreviousStatus($result["monitorId"]);

                if ($previousStatus != HTTP_OK && $result['status'] == HTTP_OK) {
                    $message = "SERVER ONLINE AGAIN - " . $message;
                    $alert = true;
                }
                
                $this->updateMonitorInfo($result["monitorId"], $statusCode, $result, $result['time']);

                if ($result['status'] != HTTP_OK) {
                    $message = "SERVER ERROR: " . $message;
                    $alert = true;
                }
                
                if ($sendNotifications && $alert)
                    $this->sendAlert($notifications, $message);
            }
        }
    }

    /**
     * HTTP Tester - For specific Urls
     */
    private function testHttp($serverName, $serverId, $ipAddress, $notifications) {
        $httpMonitors = $this->getHttpMonitors($serverId);
        $monitorType= "HTTP/HTTPS"; //TODO: Add to Contants

        $sendNotifications = false;

        //If we have notifications configured then send notifications if needed
        if (!empty($notifications))
            $sendNotifications = true;

        foreach ($httpMonitors as $httpMonitor) {
            //See if we need to run it
            if ($httpMonitor["nextRun"] > date("Y-m-d H:i:s") && !empty($httpMonitor["nextRun"])) {
                continue;
            }

            $httpTester = new \helpers\HttpTester($httpMonitor["url"]);

            $result = $httpTester->testUrl();

            if (!LogService::logResponse($serverId, $serverName, $monitorType, $result['status'], '', $result['time'])) {
                \Tina4\Debug::message("Failed to log response. See previous debug message.");
            }
            else {
                $alert = false;
                $forceNotification = false;
                $message = "UPTIMEMONITOR.AI\nSERVER: {$serverName}\nIP: {$ipAddress}\nURL: {$httpMonitor['url']}\STATUS: {$result['status']}\nSERVER TIME: " . date('Y-m-d H:i:s') . "\nRESPONSE TIME: {$result['time']}";
                
                //Get the previous monitor info to see if need to send a slack message if the server is online again
                $previousStatus = $this->getPreviousStatus($httpMonitor["id"]);

                if ($previousStatus != HTTP_OK && $result['status'] == HTTP_OK) {
                    $message = "SERVER ONLINE AGAIN - " . $message;
                    $alert = true;
                    $forceNotification = true; //Used to force a notification even if the threshold has been reached
                }
                
                $this->updateMonitorInfo($httpMonitor["id"], $result['status'], '', $result['time']);

                if ($result['status'] != HTTP_OK) {
                    $message = "SERVER ERROR: " . $message;
                    $alert = true;
                }
                
                if ($sendNotifications && $alert) {
                    $this->sendAlert($notifications, $message, $httpMonitor['notificationCount'], $forceNotification);
                }
            }
        }
    }

    /**
     * Get the previous status of a monitor
     */
    private function getPreviousStatus($monitorId) {
        $serverMonitor = new \ServerMonitor();
        if ($serverMonitor->load("id = ?", [$monitorId])) {
            return $serverMonitor->lastStatusCode;
        }
        return HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Send a message to Slack
     */
    private function sendSlackMessage($message, $slackUrl) {
        $slackMessage = $message;
        //$slackHelper = new \helpers\SlackHelper();
        //$slackHelper->postMessage($slackMessage, $slackUrl);

        \helpers\SlackHelper::postMessage($slackMessage, $slackUrl);
    }

    /**
     * Send alert
     */
    private function sendAlert($notifications, $message, $notificationCount = 0, $forceNotification = false) {
        foreach ($notifications as $notification) {
            switch ($notification['notificationtypeId']) {
                case 1: //Email
                    $toAddress = ["name" => $notification['emailAddress'], "email" => $notification['emailAddress']];
                    
                    $messegerHelper = new \helpers\MessengerHelper();

                    //Only send notifications if the threshold has not been reached - this is for failed connections
                    if ($forceNotification || $notificationCount < $notification['threshold'])
                        $messegerHelper->sendMail($toAddress, "Uptime Monitor Alert", $message);
                    break;
                case 2: //Slack
                    if ($forceNotification || $notificationCount < $notification['threshold'])
                        $this->sendSlackMessage($message, $notification['slackUrl']);
                    break;
            }
        }

    }
}