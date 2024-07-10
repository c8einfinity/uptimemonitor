<?php


\Tina4\Get::add("/api/logs/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/logs/grid.twig"), HTTP_OK, TEXT_HTML);
});

/**
 * CRUD Prototype Log Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
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
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>logGrid.ajax.reload(null, false); showMessage ('Log Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>logGrid.ajax.reload(null, false); showMessage ('Log Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>logGrid.ajax.reload(null, false); showMessage ('Log Deleted');</script>"];
        break;
    }
});