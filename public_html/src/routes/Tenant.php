<?php
/**
 * Copyright © 2024 - Code Infinity. All right reserved.
 *
 * @author Philip Malan <philip@codeinfinity.co.za>
 */

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
    //Filter everthing based on a user - there is a user -> tenant (workspace) relationship
    if (!empty($filter['where']))
        $filter['where'] = getWhereFilter($filter['where']);
    else $filter['where'] = getWhereFilter("");
    
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

            return $tenant->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "update":
            $tenant->updated_at = date("Y-m-d H:i:s");
            
            break;
        case "create":
            $result = checkRequiredFields($request, $tenant->requiredFields);
            
            if ($result->httpCode != HTTP_OK)
            {
                //If we are using the API
                if (empty($request->data->formToken))
                    exit(json_encode($result)); //Terminate the script
                else {
                    //TODO: Add Error Message here
                }
            }
        break;
        case "afterCreate":
        case "afterUpdate":
            //Make sure the tenant is associated with a user
            $usertenant = new UserTenant();

            $usertenant = $usertenant->select("*", 1, 0)
                ->where("tenant_id = ? and user_id = ?", [$tenant->id, $_SESSION["userid"]])
                ->asObject();

            if (!empty($usertenant[0]->id)) { //TODO: Ask @Andre if there is a way to get a single object
                $usertenant = $usertenant[0];
                $usertenant->updated_at = date("Y-m-d H:i:s");
            }
            else {
                $usertenant = new UserTenant();
                $usertenant->tenant_id = $tenant->id;
                $usertenant->user_id = $_SESSION["userid"]; 
            }

            $usertenant->save();

            if (!empty($request->data->formToken))
                return (object)["httpCode" => HTTP_OK, "message" => "<script>tenantGrid.ajax.reload((null, false));</script>"];
            else return $tenant->asObject();
        break;
        case "delete":
            //TODO: Check if there are servers associated with the workspace then it cannot be deleted


            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => HTTP_BAD_REQUEST, "message" => "Tenant ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            return (object)["httpCode" => HTTP_OK, "message" => "Tenant Deleted"];
        break;
    }
});