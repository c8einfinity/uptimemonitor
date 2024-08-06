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
    