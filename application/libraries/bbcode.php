<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bbcode
 *
 * @author antoine
 */

require_once 'bbcode/bbcode_parser.php';

class bbcode {
    
    private $bbcode_parser;
    
    public function __construct() {
        $this->bbcode_parser = new parser;
    }
    
    public function parse($s)
    {
        return $this->bbcode_parser->p($s, 1);
    }
}

?>
