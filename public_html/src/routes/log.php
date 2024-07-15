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
        
            return   $log->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            try {
                $log = (new Log());

                $requiredFields = array(
                    "serverId",
                    "monitorType",
                    "statusCode",
                    "rawResult"
                );

                //Check all required fields - helpers/general.php
                $result = buildObject($request, $requiredFields);

                if ($result->httpCode != HTTP_OK)
                    return $result;
                
                $log = $result;

                if ($log->save())
                    return (object)["httpCode" => HTTP_OK, "message" => "Log Created or Updated"];
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "Log Creation or Update Failed"];
            }   
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            return $log->asObject();
        break;
        case "delete":
            try {
                if (empty($request->inlineParams[0]))
                    return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "Log ID is required"];
                
                $log = (new Log());
                $log->id = $request->inlineParams[0];

                if ($log->load()) {
                    $log->delete();
                    return (object)["httpCode" => HTTP_OK, "message" => "Log Deleted"];
                }
                else return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "Log not found"];
            
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterDelete":
            return (object) ["httpCode" => 200, "message" => "Log Entry Deleted"];
        break;
    }
});