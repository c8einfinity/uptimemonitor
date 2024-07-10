<?php


\Tina4\Get::add("/api/sites-monitor/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/api/sites-monitor/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype SiteMonitor Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/api/sites-monitor", new SiteMonitor(), function ($action, SiteMonitor $siteMonitor, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add SiteMonitor";
                $savePath =  TINA4_SUB_FOLDER . "/api/sites-monitor";
                $content = \Tina4\renderTemplate("/api/sites-monitor/form.twig", []);
            } else {
                $title = "Edit SiteMonitor";
                $savePath =  TINA4_SUB_FOLDER . "/api/sites-monitor/".$siteMonitor->id;
                $content = \Tina4\renderTemplate("/api/sites-monitor/form.twig", ["data" => $siteMonitor]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#siteMonitorForm').valid() ) { saveForm('siteMonitorForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $siteMonitor->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>siteMonitorGrid.ajax.reload(null, false); showMessage ('SiteMonitor Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>siteMonitorGrid.ajax.reload(null, false); showMessage ('SiteMonitor Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>siteMonitorGrid.ajax.reload(null, false); showMessage ('SiteMonitor Deleted');</script>"];
        break;
    }
});