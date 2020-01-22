<?php
namespace Application\Parser;
use Application\Adapter\Adapter;

abstract class Parser {
    protected $needleFields;
    protected $date;
    public function __construct(){
    }
    public function parse($entry){
            $needleFields = $this->needleFields;
            $fieldsToInsert = [];
			array_walk($needleFields, function($needIndex, $index) use (&$fieldsToInsert, $entry) {
				if(isset($entry[$needIndex]) && !empty($entry[$needIndex])) {
                    $value = $entry[$needIndex];
                    if($index == 0) $value = $this->validateValueID($value);
                    if($index == 1) $value = $this->validatePrice($value);
                    if(!$value) return false;
                    $fieldsToInsert[] = $value;
                }else{
                    return false;
                }
            });
            $fieldsToInsert[] = $this->date;
            if(count($fieldsToInsert) < 4) return false;
            return $fieldsToInsert;
    }
    protected function validateValueID($id_value = false){
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