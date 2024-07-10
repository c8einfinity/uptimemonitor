<?php


\Tina4\Get::add("/api/sites/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/sites/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype Site Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/sites", new Site(), function ($action, Site $site, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add Site";
                $savePath =  TINA4_SUB_FOLDER . "/api/sites";
                $content = \Tina4\renderTemplate("/api/sites/form.twig", []);
            } else {
                $title = "Edit Site";
                $savePath =  TINA4_SUB_FOLDER . "/api/sites/".$site->id;
                $content = \Tina4\renderTemplate("/api/sites/form.twig", ["data" => $site]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#siteForm').valid() ) { saveForm('siteForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $site->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>siteGrid.ajax.reload(null, false); showMessage ('Site Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>siteGrid.ajax.reload(null, false); showMessage ('Site Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>siteGrid.ajax.reload(null, false); showMessage ('Site Deleted');</script>"];
        break;
    }
});