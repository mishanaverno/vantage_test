<?php
namespace Application\Parser;

require_once (APP_PATH . "/Parser/Parser.php");
require_once (APP_PATH . "/Parser/ParserEU.php");
require_once (APP_PATH . "/Parser/ParserUS.php");

abstract class ParserFactory {
    protected static $inst;
    protected $needleFields;
    public static function getParser($parser){
        //exception
            $class = "Application\Parser\Parser".mb_strtoupper($parser);
            return new $class();
    }
    
}