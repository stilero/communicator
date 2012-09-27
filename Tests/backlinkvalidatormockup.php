<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class BacklinkValidatorMockup extends BacklinkValidator{
    
    public function __construct($Link, $Linker, $config = "") {
        parent::__construct($Link, $Linker, $config);
    }
    
    public function findBacklinks(){
        $this->_findBackLinks();
    }
}
?>
