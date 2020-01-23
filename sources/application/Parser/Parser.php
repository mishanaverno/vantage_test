<?php
namespace Application\Parser;
use Application\Adapter\Adapter;
use Application\Log\Logger;

abstract class Parser {
    protected $needleFields;
    protected $date;
    public function __construct(){
    }
    public function parse($entry){
        try{
            if(!$this->date) throw new \ErrorException("Date was not set in parser", 1);
        } catch (\ErrorException $e){
            Logger::getInst()->warn("Error is thrown with message - " . $e->getMessage());
            exit;
        }
        $needleFields = $this->needleFields;
        $fieldsToInsert = [];
        array_walk($needleFields, function($needIndex, $index) use (&$fieldsToInsert, $entry) {
            if(isset($entry[$needIndex]) && !empty($entry[$needIndex])) {
                $value = $entry[$needIndex];
                if($index == 0) $value = $this->validateValueId($value);
                if($index == 1) $value = $this->validatePrice($value);
                $fieldsToInsert[] = $value;
            }
        });
        $fieldsToInsert[] = $this->date;
        return $fieldsToInsert;
        
    }
    protected function validateValueId($id_value = false){
        $market_id = 0;
        $query = "SELECT market_id FROM markets WHERE id_value = ?";
        $value = Adapter::getInst()->exec($query,[$id_value])->fetch();
        return $value;
    }
    protected function validatePrice($value){
        preg_match("/[1-9+][0-9]*/",$value, $match);
        return $match[0];
    }
    public function setDate($date){
        $this->date = $date;
        return $this;
    }
}