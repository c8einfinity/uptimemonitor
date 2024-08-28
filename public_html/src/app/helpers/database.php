<?php
    /**
     * Database helper functions to ensure we don't repeat code
     */

     function getServerMonitors($filter, $serverId = 0) { //TODO: Add functionality for a single server
        $serverMonitor = new \ServerMonitor();

        if (!empty($filter['where']))
            $serversFilter = getUserServersFilter($filter['where']);
        else $serversFilter = getUserServersFilter("");

        $serverMonitors = $serverMonitor->select("*", $filter["length"], $filter["start"])
            ->where("{$serversFilter}")
            ->orderBy($filter["orderBy"])
            ->filter(static function(ServerMonitor $data) {
                $server = (new Server())->load("id = ?", [$data->serverId])->asObject();
                $data->serverName = $server->serverName;
            })
            ->filter(static function(ServerMonitor $data) {
                $monitorType = (new MonitorType())->load("id = ?", [$data->monitorTypeId])->asObject();
                $data->monitorType = $monitorType->monitorType;
            })
            ->asResult();

            return $serverMonitors;
     }

     /**
      * Get the server id from the server name - Used for filtering etc
      */
     function getServerIdFromServerName($serverName) {
        $server = new Server();
        $server = $server->load("server_name LIKE %?%", [$serverName])->asObject();

        return $server->id;
     }

     /**
      * Get the monitor type id from the monitor name - Used for filtering etc
      */
     function getMonitorTypeIdFromMonitorName($monitorName) {
        $monitorType = new MonitorType();
        $monitorType = $monitorType->load("monitor_type = ?", [$monitorName])->asObject();

        return $monitorType->id;
     }