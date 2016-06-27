#!/usr/bin/env php
<?php

include_once('cron.php');

use utilities\cron\cron;

class myCron extends cron {
    /**
     * Code to be executed
     */
    protected function execute() {
        $this->log( "Hello Cron World");
    }
}

new myCron('/var/logs/example.log');