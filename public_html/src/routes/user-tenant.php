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
            $result = checkRequiredFields($request, $userTenant->requiredFields);

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $userTenant->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => 400, "message" => "UserTenant ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "UserTenant Deleted"];
    }
});