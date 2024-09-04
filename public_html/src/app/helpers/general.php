<?php
//Function to build an object and check for required fields
/*function buildObject($request, $requiredFields) {
    $object = new \stdClass();

    //Check for an empty request
    if (empty($request->data)) {
        return (object)[HTTP_BAD_REQUEST, "message" => "Some required fields are missing"];
    }

    //Now check if we have all the required fields
    foreach ($requiredFields as $requiredField) {
        if (!array_key_exists($requiredField, (array)$request->data)) {
            return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "{$requiredField} is required"];
        }
    }
    
    //Now build the object and return it
    foreach ($request->data as $key => $value) {
        //Do some validation
        if (empty($value)) {
            if (in_array($requiredFields, $key)) {
                return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "{$key} is required"];
            }
            else $object->$key = $value;
        }
        else $object->$key = $value;

        return $object;
    }
}*/

//Function to check for required fields in routes
function checkRequiredFields($request, $requiredFields) {
    //Check for an empty request
    if (empty($request->data)) {
        return (object)[HTTP_BAD_REQUEST, "message" => "Some required fields are missing"];
    }

    //Now check if we have all the required fields
    foreach ($requiredFields as $requiredField) {
        if (!property_exists($request->data, $requiredField) || empty($request->data->$requiredField)) {
            return (object)["httpCode" => HTTP_BAD_REQUEST, "message" => "{$requiredField} is required"];
        }
    }

    return (object)["httpCode" => HTTP_OK];
}

/**
 * Where filter for tenants to ensure we only get data for the user and tenant
 */
function getWhereFilter($filter, $field = 'id') {
    if (!empty($filter)) {
        $filter .= " AND ({$field} in (select tenant_id from user_tenant where user_id = {$_SESSION['userid']}))";
    }
    else {
        $filter = "{$field} in (select tenant_id from user_tenant where user_id = {$_SESSION['userid']})";
    }

    return $filter;
}

/**
 * Filter to get users that are linked to a specific account / admin user
 */
function getUsersFiter($filter, $field = 'id') {
    if (!empty($filter))
        $filter .= ' AND id = '.$_SESSION["userid"];
    else
        $filter = 'id = '.$_SESSION["userid"];

    return $filter;
}

/**
 * Get the tenants linked to a specific user
 */
function getUserTenants() {
    $usertenant = new UserTenant();

    $usertenants = $usertenant->select("tenant_id")
        ->where("user_id = ?", [$_SESSION["userid"]])
        ->asArray();

    //Only return the tenant_ids
    return array_column($usertenants, "tenant_id");
}

/**
 * Get the servers for a user
 */
function getUserServersFilter($filter) {
    $tenants = getUserTenants();

    if (!empty($tenants)) {
        $server = new Server();

        $tenant_list = implode(',', $tenants);

        $servers = $server->select("id")
            ->where("tenant_id in ({$tenant_list})")
            ->asArray();

        if (!empty($servers)) {
            $servers = array_column($servers, "id");

            $server_list = implode(',', $servers);

            $str = "server_id in ({$server_list})";
        }
        else $str = "server_id = -1"; //Ensure we return no records
    
        if (!empty($filter))
            $filter .= ' and '.$str;
        else $filter = $str;

        return $filter;
    }
    else return "server_id = -1";
}



