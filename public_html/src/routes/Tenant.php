<?php

\Tina4\Get::add("/api/tenants/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/tenants/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Tenant Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description Tenants
 * @tags Tenants
 */
\Tina4\Crud::route ("/api/tenants", new Tenant(), function ($action, Tenant $tenant, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Tenant";
                $savePath =  TINA4_SUB_FOLDER . "/api/tenants";
                $content = \Tina4\renderTemplate("/api/tenants/form.twig", []);
            } else {
                $title = "Edit Tenant";
                $savePath =  TINA4_SUB_FOLDER . "/api/tenants/".$tenant->id;
                $content = \Tina4\renderTemplate("/api/tenants/form.twig", ["data" => $tenant]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#tenantForm').valid() ) { saveForm('tenantForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $tenant->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            $result = checkRequiredFields($request, $tenant->requiredFields);

            //Set the updated_at field if tenant already exists
            if ($tenant->id > 0)
                $tenant->updated_at = date("Y-m-d H:i:s");

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $tenant->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "Tenant ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "Tenant Deleted"];
        break;
    }
});