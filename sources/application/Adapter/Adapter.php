<?php

namespace Application\Adapter;

require_once (APP_PATH . "/Conf/Config.php");
require_once (APP_PATH . "/Log/Logger.php");

use Application\Conf\Config as Conf;
use Application\Log\Logger as Logger;
use ErrorException;

class Adapter {

	private static $inst;

	/**
	 * @var \PDO
	 */
	private $connection;
	private $stmt;
	private $queue;

	private function __construct(){
		$this->queue = [];
	}

	public static function getInst() {
		if (!isset(self::$inst)){
			self::$inst = new self();
		}

		return self::$inst;
	}

	public function getConnection() {
		$conf = Conf::getInst()->getConf();
		$this->connection = new \PDO("mysql:host={$conf->db->host};dbname={$conf->db->name}", $conf->db->user, $conf->db->password);
	}

	public function dropConnection() {
		if (isset($this->connection)) {
			$this->connection = null;
		}
	}

	public function exec($query, $args = array()) {
		try {
			$this->getConnection();
			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			$this->stmt = $this->connection->prepare($query);
			if (!is_array($args)) {
				$args = array($args);
			}
			$this->stmt->execute($args);
			$this->dropConnection();
		} catch (\PDOException $e) {
			Logger::getInst()->debug("Error is thrown with message - " . $e->getMessage());
		}
		return $this;
	}
	public function fetch(){
		if($row = $this->stmt->fetch(\PDO::FETCH_NUM)){
			if(is_array($row) && count($row) == 1) 
				$row = $row[0];
			return $row;
		}
	}
	public function addToQueue($query, $args){
		$this->queue[] = (object) [
			'query' => $query,
			'args' => $args
		];
		return $this;
	}
	public function execQueue(){
		$counter = 0;
		array_walk($this->queue,function($task) use (&$counter) {
			if($this->exec($task->query,$task->args)->isComplete()){
				$counter++;
			}
		});
		$this->queue = [];
		return $counter;
	}
	public function isComplete(){
		return $this->stmt->errorCode() == "00000";
	}
	public function queueLength(){
		return count($this->queue);
	}

}