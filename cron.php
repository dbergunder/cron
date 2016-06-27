<?php

/**
 * Abstract Class cron
 * Boilerplate for Cron tasks running in PHP
 * Does loading and testing of permissions for log file
 * Logs message to log file
 * Terminates task if any errors occur
 * TODO: PSR-3 / PSR Log Compliance?
 */
namespace utilities\cron;

abstract class cron
{
    /**
     * @var string
     */
    protected $lineStart;

    /**
     * @var string
     */
    protected $logFile;

    /**
     * @var bool
     */
    protected $terminate = false;

    /**
     * cron constructor.
     * @param string $logFile
     * @param string $defaultTimeZone
     * @param string $dateFormat
     */
    public function __construct($logFile = 'error_log', $defaultTimeZone = 'America/Chicago', $dateFormat = "F j, Y, g:i a e O") {
        date_default_timezone_set($defaultTimeZone);
        $this->lineStart = '[ '. date($dateFormat) . ' ] Cron Log: ';
        $this->logFile = $logFile;

        $this->startUp();

        if(!$this->terminate) {
            $this->execute();
        }

        $this->shutDown();
    }

    /**
     * Log
     * @param string $message
     */
    public function log($message) {
        echo $message . PHP_EOL;
        if ($this->terminate || $this->logFile === 'error_log') {
            error_log($this->lineStart . $message, 0);
        } else {
            error_log($this->lineStart . $message . PHP_EOL, 3, $this->logFile);
        }
    }

    /**
     * Test the file for read/write/exist
     * @param string $message
     * @return bool
     */
    protected function testLog($message = '') {
        $success = false;

        if ($this->logFile === 'error_log') {
            $success = true;
        } else if (file_exists($this->logFile)) {
            if(is_writeable($this->logFile)) {
                $success = true;
            } else {
                $this->terminate = true;
                $message = "Cron log $this->logFile not writable.";
            }
        } else {
            if(false !== file_put_contents($this->logFile, 'Log File Create on ' . date("F j, Y, g:i a e O") . PHP_EOL))  {
                $success = true;
            } else {
                $this->terminate = true;
                $message = "Cron log $this->logFile could not be created or written to.";
            }
        }

        $this->log($message);
        return $success;
    }

    /**
     * Startup point before executing any cron code
     */
    protected function startUp() {
        $this->testLog('Cron Job Started.');
    }

    /**
     * Shutdown method after execution
     */
    protected function shutDown() {
        $this->log('Cron Job Shutting Down.');
    }

    /**
     * Code to be executed
     */
    protected function execute() {
        // Override and do code here
    }
}