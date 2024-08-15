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
    //Get the servers for a user
    if (!empty($filter['where']))
        $filter['where'] = getUserServersFilter($filter['where']);
    else $filter['where'] = getUserServersFilter("");
    
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
            //Get the servers
            $filter = str_replace('server_id', 'id', $filter); //Bit of a hack - Want to use the same query but the server_id is actually the id in the server table

            $server = new Server();
            $servers = $server->select("id, server_name")
                ->where($filter['where'])
                ->orderBy("server_name")
                ->asArray();

            //Get the notification types
            $notificationType = new NotificationType();
            $notificationTypes = $notificationType->select("id, notification_type")
                ->orderBy("monitor_type")
                ->asArray();
             
            if ($action == "form") {
                $title = "Add Notification";
                $savePath =  TINA4_SUB_FOLDER . "/api/notifications";
                $content = \Tina4\renderTemplate("/api/notifications/form.twig", ["servers" => $servers, "notificationtypes" => $notificationTypes]);
            } else {
                $title = "Edit Notification";
                $savePath =  TINA4_SUB_FOLDER . "/api/notifications/".$notification->id;
                $content = \Tina4\renderTemplate("/api/notifications/form.twig", ["data" => $notification, "servers" => $servers, "notificationtypes" => $notificationTypes]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#notificationForm').valid() ) { saveForm('notificationForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $notification->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->filter(static function(Notification $data) {
                    $server = (new Server())->load("id = ?", [$data->serverId])->asObject();
                    $data->serverName = $server->serverName;
                })
                /*->filter(static function(Notification $data) {
                    $notificationType = (new NotificationType())->load("id = ?", [$data->notificationTypeId])->asObject();
                    $data->notificationType = $notificationType->notificationType;
                })*/ //TODO: Fix this query
                /*->filter(static function(Notification $data) {
                    $tenant = (new Tenant())->load("id = ?", [$data->tenantId])->asObject();
                    $data->tenantName = $tenant->tenantName;
                })*/ //TODO: Fix this query
                ->asResult();
        break;
        case "create":
            $result = checkRequiredFields($request, $notification->requiredFields);

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $notification->asObject();
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