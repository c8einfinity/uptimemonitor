<?php

\Tina4\Get::add("/api/userroletypes/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/userroletypes/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype UserRoleType Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/userroletypes", new UserRoleType(), function ($action, UserRoleType $userRoleType, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add UserRoleType";
                $savePath =  TINA4_SUB_FOLDER . "/api/userroletypes";
                $content = \Tina4\renderTemplate("/api/userroletypes/form.twig", []);
            } else {
                $title = "Edit UserRoleType";
                $savePath =  TINA4_SUB_FOLDER . "/api/userroletypes/".$userRoleType->id;
                $content = \Tina4\renderTemplate("/api/userroletypes/form.twig", ["data" => $userRoleType]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#userRoleTypeForm').valid() ) { saveForm('userRoleTypeForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $userRoleType->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userRoleTypeGrid.ajax.reload(null, false); showMessage ('UserRoleType Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userRoleTypeGrid.ajax.reload(null, false); showMessage ('UserRoleType Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>userRoleTypeGrid.ajax.reload(null, false); showMessage ('UserRoleType Deleted');</script>"];
        break;
    }
});