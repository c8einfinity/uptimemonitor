<?php

\Tina4\Get::add("/api/servermonitors/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/servermonitors/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype ServerMonitor Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description ServerMonitors
 * @tags ServerMonitors
 */
\Tina4\Crud::route ("/api/servermonitors", new ServerMonitor(), function ($action, ServerMonitor $serverMonitor, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add ServerMonitor";
                $savePath =  TINA4_SUB_FOLDER . "/api/servermonitors";
                $content = \Tina4\renderTemplate("/api/servermonitors/form.twig", []);
            } else {
                $title = "Edit ServerMonitor";
                $savePath =  TINA4_SUB_FOLDER . "/api/servermonitors/".$serverMonitor->id;
                $content = \Tina4\renderTemplate("/api/servermonitors/form.twig", ["data" => $serverMonitor]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#serverMonitorForm').valid() ) { saveForm('serverMonitorForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $serverMonitor->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            $result = checkRequiredFields($request, $serverMonitor->requiredFields);

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $serverMonitor->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "ServerMonitor ID is required"]));
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => HTTP_OK, "message" => "ServerMonitor Deleted"];
        break;
    }
});