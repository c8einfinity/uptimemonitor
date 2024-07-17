<?php

\Tina4\Get::add("/api/usertenants/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/usertenants/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype UserTenant Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description UserTenants
 * @tags UserTenants
 */
\Tina4\Crud::route ("/api/usertenants", new UserTenant(), function ($action, UserTenant $userTenant, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add UserTenant";
                $savePath =  TINA4_SUB_FOLDER . "/api/usertenants";
                $content = \Tina4\renderTemplate("/api/usertenants/form.twig", []);
            } else {
                $title = "Edit UserTenant";
                $savePath =  TINA4_SUB_FOLDER . "/api/usertenants/".$userTenant->id;
                $content = \Tina4\renderTemplate("/api/usertenants/form.twig", ["data" => $userTenant]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#userTenantForm').valid() ) { saveForm('userTenantForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $userTenant->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
        case "afterUpdate":
            return $userTenant->asObject();
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userTenantGrid.ajax.reload(null, false); showMessage ('UserTenant Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "UserTenant Deleted"];
    }
});