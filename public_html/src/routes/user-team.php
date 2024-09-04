<?php

\Tina4\Get::add("/api/userteams/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/userteams/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype UserTeam Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/userteams", new UserTeam(), function ($action, UserTeam $userTeam, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add UserTeam";
                $savePath =  TINA4_SUB_FOLDER . "/api/userteams";
                $content = \Tina4\renderTemplate("/api/userteams/form.twig", []);
            } else {
                $title = "Edit UserTeam";
                $savePath =  TINA4_SUB_FOLDER . "/api/userteams/".$userTeam->id;
                $content = \Tina4\renderTemplate("/api/userteams/form.twig", ["data" => $userTeam]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#userTeamForm').valid() ) { saveForm('userTeamForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $userTeam->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userTeamGrid.ajax.reload(null, false); showMessage ('UserTeam Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userTeamGrid.ajax.reload(null, false); showMessage ('UserTeam Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>userTeamGrid.ajax.reload(null, false); showMessage ('UserTeam Deleted');</script>"];
        break;
    }
});