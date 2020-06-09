<?php

class Logger {

    public static $log_levels = [
        "ERROR" => [
            "NUMBER" => 0,
            "FILE_NAME" => "error_log"
        ],
        "INFO" => [
            "NUMBER" => 1,
            "FILE_NAME" => "info_log"
        ],
        "WARNING" => [
            "NUMBER" => 2,
            "FILE_NAME" => "warning_log"
        ]
    ];

    public const LOG_LEVEL_ERROR = 0;
    public const LOG_LEVEL_INFO = 1;
    public const LOG_LEVEL_WARNING = 2;

    private static $instance;

    private function __construct() {
        $this->logFiles = [
            $_SERVER['DOCUMENT_ROOT'] . "/logs/error_log.txt",
            $_SERVER['DOCUMENT_ROOT'] . "/logs/info_log.txt",
            $_SERVER['DOCUMENT_ROOT'] . "/logs/warning_log.txt",
        ];

        $this->checkFiles();
    }

    private function checkFiles() {
        foreach(self::$log_levels as $log_level) {
            if(file_exists($this->logFiles[$log_level['NUMBER']])) {
                if(filesize($this->logFiles[$log_level['NUMBER']]) > 20000) { #20kB, 1000000 = 10 MB
                    $i = 0;
                    $file = $this->getNextFile($i, $log_level['FILE_NAME']);

                    while(file_exists($file)) {
                        $file = $this->getNextFile($i, $log_level['FILE_NAME']);
                        $i++;
                    }
        
                    # Move old file
                    rename($this->logFiles[$log_level['NUMBER']], $file);
                }
            }
        }
    }

    private function getNextFile($index, $logfile) {
        return $_SERVER['DOCUMENT_ROOT'] . "/logs/" . $logfile . $index . ".txt";
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function write_log($msg, $_LOG_LEVEL) {
        $msg = sprintf("[ %s ] %s %s\n", date("H:i:s d.m.Y"), $_SERVER['REMOTE_ADDR'], $msg);
        file_put_contents($this->logFiles[$_LOG_LEVEL], $msg, FILE_APPEND);
    }

}

?>