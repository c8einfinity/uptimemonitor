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
            //Manipulate the $object here
        break;
        case "afterCreate":
        case "afterUpdate":
            return $serverMonitor->asObject();
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>serverMonitorGrid.ajax.reload(null, false); showMessage ('ServerMonitor Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "ServerMonitor Deleted"];
        break;
    }
});