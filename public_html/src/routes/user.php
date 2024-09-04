<?php
/**
 * Copyright © 2024 - Code Infinity. All right reserved.
 *
 * @author Philip Malan <philip@codeinfinity.co.za>
 */

\Tina4\Get::add("/api/users/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/users/grid.twig"), HTTP_OK, TEXT_HTML);
});

\Tina4\Get::add("/api/users/logout", function (\Tina4\Response $response){
    session_destroy();
    unset($_SESSION);
    return $response(["httpCode" => HTTP_OK, "action" => "redirect", "url" => "/"]);
});

\Tina4\Post::add("/api/users/login", function (\Tina4\Response $response, \Tina4\Request $request) {
    $user = new User();
    $user->username = $request->data->username;
    $user->password = $request->data->password;

    $userExists = $user->select("id, default_timezone_id")
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
        $_SESSION['defaultTimezoneId'] = $userExists[0]->defaultTimezoneId;

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
    /**
     * Filter only on users you have access to
     */
    if (!empty($filter['where']))
        $filter['where'] = getUsersFiter($filter['where']);
    else $filter['where'] = getUsersFiter("");
    
    
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
            $timezone = new Timezone();
            $timezones = $timezone->select("id, timezone", 500)
                ->orderBy("timezone")
                ->asArray();

            $userRoleTypes = new UserRoleType();
            $userRoleTypes = $userRoleTypes->select("id, user_role_type")
                ->orderBy("user_role_type")
                ->asArray();

            $userRole = new UserRole();
            $userRole = $userRole->select("*", 1, 0) //Later we expand this so that users have multiple roles etc for different tenants
                ->where("user_id = ?", [$user->id])->asObject();

            if (!empty($userRole))
                $user->userRoleId = $userRole[0]->userRoleId;
            else $user->userRoleId = 0;

            if ($action == "form") {
                $title = "Add User";
                $savePath =  TINA4_SUB_FOLDER . "/api/users";
                $content = \Tina4\renderTemplate("/api/users/form.twig", ["timezones" => $timezones, "userroletypes" => $userRoleTypes]);
            } else {
                $title = "Edit User";
                $savePath =  TINA4_SUB_FOLDER . "/api/users/".$user->id;
                $content = \Tina4\renderTemplate("/api/users/form.twig", ["data" => $user, "timezones" => $timezones, "userroletypes" => $userRoleTypes, "userrole" => $userRole]);
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
                ->filter(static function(User $data) {
                    $userRole = (new UserRole())->load("user_id = ?", [$data->id])->asObject();
                    $data->userRoleId = $userRole->userRoleId;
                })
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

            //Add the user to the user_role table
            $userRole = new UserRole();
            $userRole->userId = $user->id;
            $userRole->userRoleTypeId = $request->data->userRoleTypeId;
            $userRole->save();

            //Populate the message
            $message = \Tina4\renderTemplate("/mail/welcome.twig", ["data" => $data]);

            $mail->sendMail($toAddress, "Welcome to Uptime Monitor", $message);
        case "afterUpdate":
            //Add the user to the user_role table
            $userRole = new UserRole();
            $userRole->load("user_id = ?", [$user->id])->asObject();
            $userRole->userRoleId = $request->data->userRoleId;
            $userRole->save();

            return $user->asObject();
        break;
        case "delete":
            if (empty($request->inlineParams[0]))
                exit(json_encode(["httpCode" => 400, "message" => "User ID is required"])); //Terminate the script
        break;
        case "afterDelete":
            //TODO: Delete from user roles

            //return needed 
            return (object)["httpCode" => HTTP_OK, "message" => "User Deleted"];
        break;
    }
});