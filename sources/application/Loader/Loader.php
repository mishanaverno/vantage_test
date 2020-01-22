<?php

namespace Application\Loader;

require_once (APP_PATH . "/Parser/ParserFactory.php");
require_once (APP_PATH . "/Adapter/Adapter.php");
require_once (APP_PATH . "/Log/Logger.php");

use Application\Log\Logger as Logger;
use Application\Adapter\Adapter;
use Application\Parser\ParserFactory;
class Loader {
	private $date;
	public function __construct(){}

	public function load($file) {
		try{
			$this->parseDate($file);
			Logger::getInst()->info("Starting to load file $file");
			if(!file_exists($file)){
				throw new \ErrorException("CSV file Not Found", 404);
			}
			$handle = fopen($file, "r");
			$fileContent = array();
			while (($data = fgetcsv($handle, "1000", ",")) !== false) {
				$fileContent[] = $data;
			}
			unset($fileContent[0]);
			Logger::getInst()->info("File load is finished");
			preg_match("/market\.(.*?)\./m",$file,$match);
			$parser = $match[1];
			$this->parse($fileContent, $parser);

			
		} catch (\ErrorException $e){
			Logger::getInst()->warn("Error is thrown with message - " . $e->getMessage());
			Logger::getInst()->info("File loading is is canceled");
		}
	}

	private function parse($content, $parser = false) {
		Logger::getInst()->info("Starting to parse file");
		$counter = 0;
		array_walk($content, function($entry) use ($parser, &$counter){
			$parser = ParserFactory::getParser($parser);
			$fieldsToInsert = $parser->setDate($this->getDate())->parse($entry);
			$query = "INSERT INTO `market_data` (id_value, price, is_noon, update_date) VALUES (?, ?, ?, ?)";
			if($fieldsToInsert !== false){
				Adapter::getInst()->exec($query,$fieldsToInsert);
				$counter++;
			}
		});

		Logger::getInst()->info("File parsing is finished. $counter rows inserted");
	}
	private function parseDate($file){
		preg_match("/\.([0-9]{8})/m",$file,$match);
		$this->date = \DateTime::createFromFormat("Ymd",$match[1])->format("Y-m-d");
	}
	private function getDate()
	{	
		return $this->date ? $this->date : date("Y-m-d"); 
	}

}
