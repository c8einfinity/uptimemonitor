<?php

\Tina4\Get::add("/api/monitortypes/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/monitortypes/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype MonitorType Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description MonitorTypes
 * @tags MonitorTypes
 */
\Tina4\Crud::route ("/api/monitortypes", new MonitorType(), function ($action, MonitorType $monitorType, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add MonitorType";
                $savePath =  TINA4_SUB_FOLDER . "/api/monitortypes";
                $content = \Tina4\renderTemplate("/api/monitortypes/form.twig", []);
            } else {
                $title = "Edit MonitorType";
                $savePath =  TINA4_SUB_FOLDER . "/api/monitortypes/".$monitorType->id;
                $content = \Tina4\renderTemplate("/api/monitortypes/form.twig", ["data" => $monitorType]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#monitorTypeForm').valid() ) { saveForm('monitorTypeForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $monitorType->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            $result = checkRequiredFields($request, $monitorType->requiredFields);

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $monitorType->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "MonitorType ID is required"]));
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "MonitorType Deleted"];
        break;
    }
});