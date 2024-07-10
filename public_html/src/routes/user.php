<?php


\Tina4\Get::add("/api/users/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/users/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype User Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/users", new User(), function ($action, User $user, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add User";
                $savePath =  TINA4_SUB_FOLDER . "/api/users";
                $content = \Tina4\renderTemplate("/api/users/form.twig", []);
            } else {
                $title = "Edit User";
                $savePath =  TINA4_SUB_FOLDER . "/api/users/".$user->id;
                $content = \Tina4\renderTemplate("/api/users/form.twig", ["data" => $user]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#userForm').valid() ) { saveForm('userForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $user->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userGrid.ajax.reload(null, false); showMessage ('User Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>userGrid.ajax.reload(null, false); showMessage ('User Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>userGrid.ajax.reload(null, false); showMessage ('User Deleted');</script>"];
        break;
    }
});