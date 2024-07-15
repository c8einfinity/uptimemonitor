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
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Notification";
                $savePath =  TINA4_SUB_FOLDER . "/api/notifications";
                $content = \Tina4\renderTemplate("/api/notifications/form.twig", []);
            } else {
                $title = "Edit Notification";
                $savePath =  TINA4_SUB_FOLDER . "/api/notifications/".$notification->id;
                $content = \Tina4\renderTemplate("/api/notifications/form.twig", ["data" => $notification]);
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
                ->asResult();
        break;
        case "create":
            try {
                $notification = (new Notification());

                $requiredFields = array(
                    "tenantId",
                    "notificationType",
                    "notificationMessage"
                );

                $result = buildObject($request, $requiredFields);

                if ($result->httpCode != HTTP_OK)
                    return $result;

                $notification = $result;

                if ($notification->save())
                    return (object)["httpCode" => HTTP_OK, "message" => "Notification Created"];
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "Notification not created"];
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }

        break;
        case "afterCreate":
        case "afterUpdate":
            return $notification->asObject();
        break;
        case "delete":
            try {
                if (empty($request->inlineParams[0]))
                    return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "Notification ID is required"];

                $notification = (new Notification());
                $notification->id = $request->inlineParams[0];
                
                if ($notification->load()) {
                    $notification->delete();
                    return (object)["httpCode" => HTTP_OK, "message" => "Notification Deleted"];
                }
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "Notification could not be deleted"];
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "Notification Deleted"];
        break;
    }
});