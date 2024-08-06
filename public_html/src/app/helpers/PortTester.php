<?php

namespace helpers;

class PortTester
{
    public $host;
    public $ports;

    public function __construct($host, $ports = [80])
    {
        $this->host = $host;
        $this->ports = $ports;
    }

    public function check(): array
    {
        \Tina4\Debug::message("Checking " . $this->host);
        $list = [];

        error_reporting(0);
        set_error_handler(null);

        foreach ($this->ports as $port) {
            try {
                //$connection = @fsockopen($this->host, $port);
                $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                $connection = @socket_connect($socket, $this->host, $port);
                if ($connection) {
                    $list[$this->host][$port] = true;
                    @socket_close($socket);
                } else {
                    $list[$this->host][$port] = false;
                }
            } catch (Exception $e) {
                $list[$this->host][$port] = false;
            }
        }

        return $list;
    }
}
