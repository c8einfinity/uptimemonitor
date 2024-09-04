<?php

\Tina4\Get::add("/api/timezones/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/timezones/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Timezone Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/timezones", new Timezone(), function ($action, Timezone $timezone, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Timezone";
                $savePath =  TINA4_SUB_FOLDER . "/api/timezones";
                $content = \Tina4\renderTemplate("/api/timezones/form.twig", []);
            } else {
                $title = "Edit Timezone";
                $savePath =  TINA4_SUB_FOLDER . "/api/timezones/".$timezone->id;
                $content = \Tina4\renderTemplate("/api/timezones/form.twig", ["data" => $timezone]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#timezoneForm').valid() ) { saveForm('timezoneForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $timezone->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>timezoneGrid.ajax.reload(null, false); showMessage ('Timezone Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>timezoneGrid.ajax.reload(null, false); showMessage ('Timezone Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>timezoneGrid.ajax.reload(null, false); showMessage ('Timezone Deleted');</script>"];
        break;
    }
});