<?php

namespace helpers;

class PortTester
{
    private $host;
    private $ports;

    public function __construct($host, $ports = null)
    {
        $this->host = $host;
        $this->ports = $ports;
    }

    public function check(): array
    {
        
        $results = array();

        foreach ($this->ports as $port) {
            try {
                $status = false;

                \Tina4\Debug::message("Checking {$this->host} for port {$port["port"]}");

                //Best way as you can set a timeout
                $connection = @fsockopen($this->host, $port["port"], $errno, $errstr, 5);

                if ($connection) {
                    $status = true;
                    fclose($connection);
                }
            } catch (\Exception $e) {
                $status = false;
            } finally {
                array_push($results, array(
                    "port" => $port["port"],
                    "status" => $status,
                    "monitorId" => $port["monitorId"]
                ));
            }
        }

        return $results;
    }
}
