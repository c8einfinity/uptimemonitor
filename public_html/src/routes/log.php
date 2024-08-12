<?php

\Tina4\Get::add("/api/logs/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/logs/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Log Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description Logs
 * @tags Logs
 */
\Tina4\Crud::route ("/api/logs", new Log(), function ($action, Log $log, $filter, \Tina4\Request $request) {
     //Get the servers for a user
    if (!empty($filter['where']))
        $filter['where'] = getUserServersFilter($filter['where']);
    else $filter['where'] = getUserServersFilter("");

    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Log";
                $savePath =  TINA4_SUB_FOLDER . "/api/logs";
                $content = \Tina4\renderTemplate("/api/logs/form.twig", []);
            } else {
                $title = "Edit Log";
                $savePath =  TINA4_SUB_FOLDER . "/api/logs/".$log->id;
                $content = \Tina4\renderTemplate("/api/logs/form.twig", ["data" => $log]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#logForm').valid() ) { saveForm('logForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }

            if (empty($filter["orderBy"]))
                $filter["orderBy"] = "created_at desc";
        
            return   $log->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->filter(static function(Log $data) {
                    $server = (new Server())->load("id = ?", [$data->serverId])->asObject();
                    $data->serverName = $server->serverName;
                })
                ->filter(static function(Log $data) {
                    $monitor = (new MonitorType())->load("id = ?", [$data->monitorType])->asObject();
                    $data->monitorName = $monitor->monitorType; //TODO: Change the db struture to monitorTypeId instead of monitorType
                })
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Check all required fields - helpers/general.php
            $result = checkRequiredFields($request, $log->requiredFields);

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $log->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "Log ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            return (object) ["httpCode" => HTTP_OK, "message" => "Log Entry Deleted"];
        break;
    }
});