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
            try {
                $monitorType = (new MonitorType());

                //Populate the required fields array
                $requiredFields = array(
                    "monitorType"
                );

                $result = buildObject($request, $requiredFields);

                if ($result->httpCode != HTTP_OK)
                    return $result;

                if ($monitorType->save())
                    return (object)["httpCode" => HTTP_OK, "message" => "MonitorType Created"];
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "MonitorType could not be created"];
            } 
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            return $monitorType->asObject();
        break;
        case "delete":
            try {
                if (empty($request->inlineParams[0]))
                    return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "MonitorType ID is required"];

                $monitorType = (new MonitorType());
                $monitorType->id = $request->inlineParams[0];

                if ($monitorType->load()) {
                    $monitorType->delete();
                    return (object)["httpCode" => HTTP_OK, "message" => "MonitorType Deleted"];
                }
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "MonitorType could not be deleted"];
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "MonitorType Deleted"];
        break;
    }
});