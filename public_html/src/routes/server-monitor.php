<?php

\Tina4\Get::add("/api/servermonitors/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/servermonitors/grid.twig"), HTTP_OK, TEXT_HTML);
});

\Tina4\Get::add("/api/servermonitors/test", function (\Tina4\Response $response){
    //Time checking is built into the monitoring class
    $monitoringService = new services\MonitoringService();
    $monitoringService->testTenants();
    return $response ("Done Testing Servers", HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype ServerMonitor Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description servermonitors
 * @tags servermonitors
 */
\Tina4\Crud::route ("/api/servermonitors", new ServerMonitor(), function ($action, ServerMonitor $serverMonitor, $filter, \Tina4\Request $request) {
    //Get the servers for a user
    /*if (!empty($filter['where']))
        $filter['where'] = getUserServersFilter($filter['where']);
    else $filter['where'] = getUserServersFilter("");*/

    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create

            if (!empty($filter['where'])) //TODO: Move this to the database.php helper
                $filter['where'] = getUserServersFilter($filter['where']);
            else $filter['where'] = getUserServersFilter("");

            //Get the servers
            $filter['where'] = str_replace('server_id', 'id', $filter['where']); //Bit of a hack - Want to use the same query but the server_id is actually the id in the server table

            $server = new Server();
            $servers = $server->select("id, server_name")
                ->where($filter['where'])
                ->orderBy("server_name")
                ->asArray();

            //Get the monitor types
            $monitortype = new MonitorType();
            $monitorTypes = $monitortype->select("id, monitor_type")
                ->orderBy("monitor_type")
                ->asArray();

            if ($action == "form") {
                $title = "Add ServerMonitor";
                $savePath =  TINA4_SUB_FOLDER . "/api/servermonitors";
                $content = \Tina4\renderTemplate("/api/servermonitors/form.twig", ["servers" => $servers, "monitortypes" => $monitorTypes]);
            } else {
                $title = "Edit ServerMonitor";
                $savePath =  TINA4_SUB_FOLDER . "/api/servermonitors/".$serverMonitor->id;
                print_r($serverMonitor);
                $content = \Tina4\renderTemplate("/api/servermonitors/form.twig", ["data" => $serverMonitor, "servers" => $servers, "monitortypes" => $monitorTypes]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#serverMonitorForm').valid() ) { saveForm('serverMonitorForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter

            //Moved it to the database.php file under helpers
            return getServerMonitors($filter);
        break;
        case "update":
            $serverMonitor->updatedAt = date("Y-m-d H:i:s");
            break;
        case "create":
            $result = checkRequiredFields($request, $serverMonitor->requiredFields);

            if ($result->httpCode != HTTP_OK)
            {
                //If we are using the API
                if (empty($request->data->formToken))
                    exit(json_encode($result)); //Terminate the script
                else exit("<script>showMessage('{$result->message}');</script>");
            } 
            else {
                //Set the active field
                $serverMonitor->active = 1;
                if (empty($serverMonitor->interval)) 
                    $serverMonitor->interval = 5;
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            //If we are using the forms
            if (!empty($request->data->formToken))
                return (object)["httpCode" => HTTP_OK, "message" => "<script>serverMonitorGrid.ajax.reload((null, false));</script>"];
            else return $serverMonitor->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "ServerMonitor ID is required"]));
        break;
        case "afterDelete":
            //return needed 
            if (!empty($request->data->formToken))
                return (object)["httpCode" => HTTP_OK, "message" => "<script>serverMonitorGrid.ajax.reload((null, false));</script>"];
            else return (object)["httpCode" => HTTP_OK, "message" => "ServerMonitor Deleted"];
        break;
    }
});