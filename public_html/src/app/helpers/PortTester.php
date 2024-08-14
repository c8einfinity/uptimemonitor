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
        \Tina4\Debug::message("Checking " . $this->host);
        $results = array();

        error_reporting(0);
        set_error_handler(null);

        foreach ($this->ports as $port) {
            try {
                $status = false;
                
                //$connection = @fsockopen($this->host, $port);
                $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                //socket_set_timeout($socket, 5, 0); //5 seconds timeout - Should be added to env file

                $connection = @socket_connect($socket, $this->host, $port["port"]);
                if ($connection) {
                   $status = true;
                    @socket_close($socket);
                } else {
                    $status = false;
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
