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
\Tina4\Crud::route ("/api/servers", new Server, function ($action, Server $server, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create


            //Get the tenants (workspaces) to populate the select box
            $tenant = new Tenant();
            $tenants = $tenant->select("id, tenant_name")
                ->where("id in (select tenant_id from user_tenant where user_id = {$_SESSION['userid']})")
                ->orderBy("tenant_name")
                ->asArray();

             
            if ($action == "form") {
                $title = "Add Server";
                $savePath =  TINA4_SUB_FOLDER . "/api/servers";
                $content = \Tina4\renderTemplate("/api/servers/form.twig", ["tenants" => $tenants]);
            } else {
                $title = "Edit Server";
                $savePath =  TINA4_SUB_FOLDER . "/api/servers/".$server->id;
                $content = \Tina4\renderTemplate("/api/servers/form.twig", ["data" => $server, "tenants" => $tenants]);
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
                ->filter(static function(Server $data) {
                    $tenant = (new Tenant())->load("id = ?", [$data->tenantId])->asObject();
                    $data->tenantName = $tenant->tenantName;
                })
                ->asResult();
        break;
        case "update":
            //Set the updated_at field
            $server->updatedAt = date("Y-m-d H:i:s");
            break;
        case "create":
            $result = checkRequiredFields($request, $server->requiredFields);

            //Set to active by default
            $server->active = 1;

            if ($result->httpCode != HTTP_OK)
            {
                //If we are using the API
                if (empty($request->data->formToken))
                    exit(json_encode($result)); //Terminate the script
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            //If we are using the forms
            if (!empty($request->data->formToken))
                return (object)["httpCode" => HTTP_OK, "message" => "<script>serverGrid.ajax.reload((null, false));</script>"];
            else return $server->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "Server ID is required"]));
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "<script>serverGrid.ajax.reload((null, false)); showMessage('Server Deleted');</script>"];
        break;
    }
});