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
            try {
                $tenant = (new Tenant());
                
                //Populate the required fields array
                $requiredFields = array(
                    "tenantName"
                );

                $result = buildObject($request, $requiredFields);

                if ($result->httpCode != HTTP_OK)
                    return $result;

                $tenant = $result;
                
                //Now save the tenant
                if ($tenant->save())
                    return (object)["httpCode" => HTTP_OK, "message" => "Tenant Created or Updated"];
                else return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => "Tenant Creation or Update Failed"];
            } catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            return $tenant->asObject();
        break;
        case "delete":
            try {
                if (empty($request->inlineParams[0]))
                    return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "Tenant ID is required"];
                
                $tenant = (new Tenant());
                $tenant->id = $request->inlineParams[0];

                if ($tenant->load()) {
                    $tenant->delete();
                    return (object)["httpCode" => HTTP_OK, "message" => "Tenant Deleted"];
                }
                else return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "Tenant not found"];
            }
            catch (\Exception $exception) {
                return (object)["httpCode" => HTTP_INTERNAL_SERVER_ERROR, "message" => $exception->getMessage()];
            }
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "Tenant Deleted"];
        break;
    }
});