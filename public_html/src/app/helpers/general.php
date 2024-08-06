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