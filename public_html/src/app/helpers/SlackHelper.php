<?php

namespace helpers;

/**
 * Class SlackHelper
 * @package helpers
 * @description This helper is used to send messages to slack
 */
class SlackHelper {
    private $slackWebhookUrl;

    public function __construct() {
        //TODO: Add this to a database
        $this->slackWebhookUrl = $_ENV["SLACK_WEBHOOK"];
    }

    public function postMessage($message) {

        try {
            $url = $this->slackWebhookUrl;

            $data = array(
                'text' => $message
            );

            $jsonData = json_encode($data);

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

            $response = curl_exec($ch);

            // Check for errors
            if ($response === false) {
                $error = curl_error($ch);
                return array(
                    "success" => false,
                    "error" => $error
                ); 
            } else {
                return array(
                    "success" => true,
                    "response" => $response
                );
            }
        }
        catch (\Exception $exception) {
            \Tina4\Debug::message("Failed to send slack message: " . $exception->getMessage());
            return false;
        }
    }
}