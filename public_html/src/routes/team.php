<?php

\Tina4\Get::add("/api/teams/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/teams/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Team Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/teams", new Team(), function ($action, Team $team, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Team";
                $savePath =  TINA4_SUB_FOLDER . "/api/teams";
                $content = \Tina4\renderTemplate("/api/teams/form.twig", []);
            } else {
                $title = "Edit Team";
                $savePath =  TINA4_SUB_FOLDER . "/api/teams/".$team->id;
                $content = \Tina4\renderTemplate("/api/teams/form.twig", ["data" => $team]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#teamForm').valid() ) { saveForm('teamForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $team->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>teamGrid.ajax.reload(null, false); showMessage ('Team Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>teamGrid.ajax.reload(null, false); showMessage ('Team Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>teamGrid.ajax.reload(null, false); showMessage ('Team Deleted');</script>"];
        break;
    }
});