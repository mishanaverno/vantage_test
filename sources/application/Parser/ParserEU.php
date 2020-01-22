<?php
namespace Application\Parser;

class ParserEU extends Parser{
    public function __construct(){
        $this->needleFields = [0, 1, 5];
    }
}