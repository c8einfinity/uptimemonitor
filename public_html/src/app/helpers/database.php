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