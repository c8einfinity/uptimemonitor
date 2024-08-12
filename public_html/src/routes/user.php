<?php

\Tina4\Get::add("/api/users/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/users/grid.twig"), HTTP_OK, TEXT_HTML);
});

\Tina4\Post::add("/api/users/login", function (\Tina4\Response $response, \Tina4\Request $request) {
    $user = new User();
    $user->username = $request->data->username;
    $user->password = $request->data->password;

    $userExists = $user->select("id")
        ->where("username = '{$user->username}'")
        ->and("password = '{$user->password}'")
        ->asObject();

    if (empty($userExists)) {
        return $response(["httpCode" => HTTP_BAD_REQUEST, "message" => "Invalid username or password or user does not exist"]);
    }
    else {
        //TODO: Add to the userlogin table

        //Set the session
        $_SESSION['loggedin'] = true;
        $_SESSION['userid'] = $userExists[0]->id;

        return $response(["httpCode" => HTTP_OK, "action" => "redirect", "url" => "/portal/dashboard"]);
    }
});
        
/**
 * CRUD Prototype User Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 * @description Users
 * @tags Users
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
            $result = checkRequiredFields($request, $user->requiredFields);

            //Check if the user exists
            $userExists = $user->select("id")
                ->where("email = '{$request->data->email}'")
                ->or("username = '{$request->data->username}'")
                ->asObject();

            if (!empty($userExists)) {
                $result = (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "User already exists"];
            }

            if ($result->httpCode != HTTP_OK)
                exit(json_encode($result)); //Terminate the script
        break;
        case "afterCreate":
            //If a new user is created then send a welcome email
            $mail = new \helpers\MessengerHelper();

            //TODO: Add Name field to form as well
            $toAddress = ["name" => $request->data->email, "email" => $request->data->email];

            $data = array(
                "username" => $request->data->username,
                "password" => $request->data->password,
                "fullname" => $request->data->fullname
            );

            //Populate the message
            $message = \Tina4\renderTemplate("/mail/welcome.twig", ["data" => $data]);

            $mail->sendMail($toAddress, "Welcome to Uptime Monitor", $message);
        case "afterUpdate":
            return $user->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => 400, "message" => "User ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => HTTP_OK, "message" => "User Deleted"];
        break;
    }
});