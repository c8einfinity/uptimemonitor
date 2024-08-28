<?php

\Tina4\Get::add("/api/notifications/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/notifications/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Notification Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description Notifications
 * @tags Notifications
 */
\Tina4\Crud::route ("/api/notifications", new Notification(), function ($action, Notification $notification, $filter, \Tina4\Request $request) {
    //Filter everthing based on a user - there is a user -> tenant (workspace) relationship
    if (!empty($filter['where']))
        $filter['where'] = getWhereFilter($filter['where'], "tenant_id");
    else $filter['where'] = getWhereFilter("", "tenant_id");
    
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
            //Get the servers
            //$filter = str_replace('server_id', 'id', $filter); //Bit of a hack - Want to use the same query but the server_id is actually the id in the server table

            /*$server = new Server();
            $servers = $server->select("id, server_name")
                ->where($filter['where'])
                ->orderBy("server_name")
                ->asArray();*/

            $tenants = new Tenant();
            $tenants = $tenants->select("id, tenant_name")
                ->where("id in (select tenant_id from user_tenant where user_id = {$_SESSION['userid']})")
                ->orderBy("tenant_name")
                ->asArray();

            //Get the notification types
            $notificationType = new NotificationType();
            $notificationTypes = $notificationType->select("id, notification_type")
                ->orderBy("notification_type")
                ->asArray();
             
            if ($action == "form") {
                $title = "Add Notification";
                $savePath =  TINA4_SUB_FOLDER . "/api/notifications";
                $content = \Tina4\renderTemplate("/api/notifications/form.twig", ["notificationtypes" => $notificationTypes, "tenants" => $tenants]);
            } else {
                $title = "Edit Notification";
                $savePath =  TINA4_SUB_FOLDER . "/api/notifications/".$notification->id;
                $content = \Tina4\renderTemplate("/api/notifications/form.twig", ["data" => $notification, "notificationtypes" => $notificationTypes, "tenants" => $tenants]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#notificationForm').valid() ) { saveForm('notificationForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return $notification->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->filter(static function(Notification $data) {
                    $notificationType = (new NotificationType())->load("id = ?", [$data->notificationtypeId])->asObject();
                    $data->notificationType = $notificationType->notificationType;
                })
                ->filter(static function(Notification $data) {
                    $tenant = (new Tenant())->load("id = ?", [$data->tenantId])->asObject();
                    $data->tenantName = $tenant->tenantName;
                })
                ->asResult();
        break;
        case "create":
            $result = checkRequiredFields($request, $notification->requiredFields);

            if ($result->httpCode != HTTP_OK)
            {
                //If we are using the API
                if (empty($request->data->formToken))
                    exit(json_encode($result)); //Terminate the script
                else exit("<script>showMessage('{$result->message}');</script>");
            }
            else {
                //Set the active field
                $notification->active = 1;
                if (empty($notification->threshold)) 
                    $notification->threshold = 5;
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            //If we are using the forms
            if (!empty($request->data->formToken))
                return (object)["httpCode" => HTTP_OK, "message" => "<script>notificationGrid.ajax.reload((null, false));</script>"];
            else return $notification->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "Notification ID is required"]));
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => HTTP_OK, "message" => "Notification Deleted"];
        break;
    }
});