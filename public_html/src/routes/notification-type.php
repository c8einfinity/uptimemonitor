<?php


\Tina4\Get::add("/api/notificationtypes/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/notificationtypes/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype NotificationType Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/notificationtypes", new NotificationType(), function ($action, NotificationType $notificationType, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add NotificationType";
                $savePath =  TINA4_SUB_FOLDER . "/api/notificationtypes";
                $content = \Tina4\renderTemplate("/api/notificationtypes/form.twig", []);
            } else {
                $title = "Edit NotificationType";
                $savePath =  TINA4_SUB_FOLDER . "/api/notificationtypes/".$notificationType->id;
                $content = \Tina4\renderTemplate("/api/notificationtypes/form.twig", ["data" => $notificationType]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#notificationTypeForm').valid() ) { saveForm('notificationTypeForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $notificationType->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>notificationTypeGrid.ajax.reload(null, false); showMessage ('NotificationType Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>notificationTypeGrid.ajax.reload(null, false); showMessage ('NotificationType Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>notificationTypeGrid.ajax.reload(null, false); showMessage ('NotificationType Deleted');</script>"];
        break;
    }
});