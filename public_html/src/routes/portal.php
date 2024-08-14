<?php
    /**
     * Portal Routes for user portal
     */

     //Check if the user is logged in
     if (isset($_SERVER['REQUEST_URI'])) {
        if (strpos($_SERVER["REQUEST_URI"], "portal") !== false && (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'])) {
            header("Location: /");
            exit();
        }
    }

    \Tina4\Get::add("/portal/dashboard", function (\Tina4\Response $response) {
        return $response (\Tina4\renderTemplate("/portal/dashboard.twig"), HTTP_OK, TEXT_HTML);
    });

    \Tina4\Get::add("/portal/summary", function (\Tina4\Response $response) {
        //Get all the monitors for a user - refer to database.php in helpers

        //TODO: Optimise this filter for paging etc and get it from the caller page
        $filter = array(
            "length" => 10,
            "start" => 0,
            "orderBy" => "created_at desc",
            "where" => ""
        );

        $serverMonitors = getServerMonitors($filter);
        
        return $response (\Tina4\renderTemplate("/portal/summary.twig", ["servermonitors" => $serverMonitors]), HTTP_OK, TEXT_HTML);
    });

    \Tina4\Post::add('/portal/checkmonitors', function(\Tina4\Response $response, \Tina4\Request $request) {
        switch ($request->data->action) {
            case 'single': //Check a single monitor
                break;
            default: //Check all monitors
                $monitoringService = new services\MonitoringService();
                $monitoringService->testTenants();
                break;
        }
        
        //TODO: Add better response
        return $response(HTTP_OK, APPLICATION_JSON);
    });
    