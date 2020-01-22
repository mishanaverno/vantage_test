<?php
namespace Application\Parser;

class ParserUS extends Parser{
    public function __construct(){
        $this->needleFields = [6, 1, 5];
    }
}