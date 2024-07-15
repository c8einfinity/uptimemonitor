<?php

\Tina4\Get::add("/api/servers/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/servers/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Server Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description Servers
 * @tags Servers
 */
\Tina4\Crud::route ("/api/servers", new Server(), function ($action, Server $server, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Server";
                $savePath =  TINA4_SUB_FOLDER . "/api/servers";
                $content = \Tina4\renderTemplate("/api/servers/form.twig", []);
            } else {
                $title = "Edit Server";
                $savePath =  TINA4_SUB_FOLDER . "/api/servers/".$server->id;
                $content = \Tina4\renderTemplate("/api/servers/form.twig", ["data" => $server]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#serverForm').valid() ) { saveForm('serverForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $server->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            try {
                $server = (new Server());

                //Populate the required fields
                $requiredFields = array(
                    "serverName",
                    "serverIP",
                    "serverPort",
                    "serverType"
                );

                $result = buildObject($request, $requiredFields);

                if ($result->httpCode != HTTP_OK)
                    return $result;

                $server = $result;

                //Now save the server
                if ($server->save())
                    return (object)["httpCode" => HTTP_OK, "message" => "Server Created"];
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "Server could not be created"];
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            return $server->asObject();
        break;
        case "delete":
            try {
                if (empty($request->inlineParams[0]))
                    return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "Server ID is required"];

                $server = (new Server());
                $server->id = $request->inlineParams[0];

                if ($server->load()) {
                    $server->delete();
                    return (object)["httpCode" => HTTP_OK, "message" => "Server Deleted"];
                }
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "Server could not be deleted"]; 
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "Server Deleted"];
        break;
    }
});