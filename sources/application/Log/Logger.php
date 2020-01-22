<?php

namespace Application\Log;

require_once (APP_PATH . "/Conf/Config.php");

use Application\Conf\Config as Conf;

class Logger {

	const LEVEL_DEBUG = "DEBUG";
	const LEVEL_INFO = "INFO";
	const LEVEL_WARN = "WARN";
	const LEVEL_ERROR= "ERROR";

	private static $inst;
	private $logFile;
	private $logFileResource;

	private function __construct(){
		$this->logFile = LOG_PATH . "/log.txt";
		$logFile = @fopen($this->logFile, "a");
		fclose($logFile);
	}

	public static function getInst() {
		if (!isset(self::$inst)){
			self::$inst = new self();
		}

		return self::$inst;
	}

	public function debug($message) {
		$conf = Conf::getInst()->getConf();
		if ($conf->logger->log_level === "debug") {
			$this->writeMessage($message, self::LEVEL_DEBUG);
		}
	}

	public function info($message) {
		$this->writeMessage($message, self::LEVEL_INFO);
	}

	public function warn($message) {
		$this->writeMessage($message, self::LEVEL_WARN);
	}

	private function openFile() {
		$this->logFileResource = @fopen($this->logFile, "a");
	}

	private function closeFile() {
		if(isset($this->logFileResource)) {
			@fclose($this->logFileResource);
		}
	}

	private function writeMessage($message, $logLevel = self::LEVEL_INFO) {
		$this->openFile();
		$currDate = date("d-m-Y h:i:s");
		$message = "[$logLevel]" . "[" . $currDate . "]: " .  $message . PHP_EOL;
		fwrite($this->logFileResource, $message);
		$this->closeFile();
	}
}