<?php

\Tina4\Get::add("/api/userlogins/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/userlogins/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype UserLogin Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description UserLogins
 * @tags UserLogins
 */
\Tina4\Crud::route ("/api/userlogins", new UserLogin(), function ($action, UserLogin $userLogin, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add UserLogin";
                $savePath =  TINA4_SUB_FOLDER . "/api/userlogins";
                $content = \Tina4\renderTemplate("/api/userlogins/form.twig", []);
            } else {
                $title = "Edit UserLogin";
                $savePath =  TINA4_SUB_FOLDER . "/api/userlogins/".$userLogin->id;
                $content = \Tina4\renderTemplate("/api/userlogins/form.twig", ["data" => $userLogin]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#userLoginForm').valid() ) { saveForm('userLoginForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $userLogin->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            $result = checkRequiredFields($request, $userLogin->requiredFields);

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
        case "afterUpdate":
            return $userLogin->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => 400, "message" => "UserLogin ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => HTTP_OK, "message" => "UserLogin Deleted"];
        break;
    }
});