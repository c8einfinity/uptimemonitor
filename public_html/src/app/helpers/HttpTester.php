<?php

namespace helpers;

use Tina4\Tina4Php;

/**
 * Class HttpTester
 * @package helpers
 * @description This class is used to test a URL to see if it is reachable
 */
class HttpTester
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     * @description This function tests a URL to see if it is reachable using curl
     */
    public function testUrl() {
        try {
            $ch = curl_init($this->url);
            
            // Set options for cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_NOBODY, true); 
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); 

        
            curl_exec($ch);

            // Check if the HTTP status code is 200 (OK)
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            

            // Close the cURL session
            curl_close($ch);

            return array(
                "status" => $httpCode,
                "time" => $totalTime
            );
        }
        catch (\Exception $e) {
            \Tina4\Debug::message("Error testing URL: {$e->getMessage()}");
            
            return array(
                "status" => 500,
                "time" => 0
            );
        }
    }
}