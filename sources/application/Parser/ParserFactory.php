<?php
namespace Application\Parser;

use Application\Log\Logger;

require_once (APP_PATH . "/Parser/Parser.php");
require_once (APP_PATH . "/Parser/ParserEU.php");
require_once (APP_PATH . "/Parser/ParserUS.php");

abstract class ParserFactory {
    protected static $inst;
    protected $needleFields;
    public static function getParser($parser){
        try{
            $class = "Application\Parser\Parser".mb_strtoupper($parser);
            if(!class_exists($class)) 
                throw new \ErrorException("Not found parser class $class", 404);
            $ref = new \ReflectionClass($class); 
            if($ref->isAbstract()) 
                throw new \ErrorException("Not found parser class in filename", 404);
            return new $class();
        }catch(\ErrorException $e){
            Logger::getInst()->warn("Error is thrown with message - " . $e->getMessage());
            exit;
        }
    }
    
}