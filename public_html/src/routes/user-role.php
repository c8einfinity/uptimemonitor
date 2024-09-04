<?php

\Tina4\Get::add("/api/userroles/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/userroles/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype UserRole Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/userroles", new UserRole(), function ($action, UserRole $userRole, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add UserRole";
                $savePath =  TINA4_SUB_FOLDER . "/api/userroles";
                $content = \Tina4\renderTemplate("/api/userroles/form.twig", []);
            } else {
                $title = "Edit UserRole";
                $savePath =  TINA4_SUB_FOLDER . "/api/userroles/".$userRole->id;
                $content = \Tina4\renderTemplate("/api/userroles/form.twig", ["data" => $userRole]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#userRoleForm').valid() ) { saveForm('userRoleForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $userRole->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userRoleGrid.ajax.reload(null, false); showMessage ('UserRole Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userRoleGrid.ajax.reload(null, false); showMessage ('UserRole Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>userRoleGrid.ajax.reload(null, false); showMessage ('UserRole Deleted');</script>"];
        break;
    }
});