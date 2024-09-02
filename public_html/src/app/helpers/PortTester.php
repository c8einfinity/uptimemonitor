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

                $startTime = microtime(true);

                $status = $this->connectToPort($port);
            } catch (\Exception $e) {
                $status = false;
            } finally {
                $endTime = microtime(true);
                $timeTaken = $endTime - $startTime;
                
                array_push($results, array(
                    "port" => $port["port"],
                    "status" => $status,
                    "monitorId" => $port["monitorId"],
                    "time" => $timeTaken
                ));
            }
        }

        return $results;
    }

    /**
     * Added this so that we can check again if it fails the 1st time
     */
    private function connectToPort($port) {
        $status = false;

        try {
            //Best way as you can set a timeout
            $connection = @fsockopen($this->host, $port["port"], $errno, $errstr, 5);

            if ($connection) {
                fclose($connection);
                $status = true;
            }
            else {
                //Try again
                sleep(5);

                $connection = @fsockopen($this->host, $port["port"], $errno, $errstr, 5);

                if ($connection) {
                    fclose($connection);
                    $status = true;
                }
            }
        }
        catch (\Exception $e) {
            $status = false;
        }
        finally {
            return $status;
        }
    }
}
