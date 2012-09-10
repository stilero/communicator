<?php
/**
 * Description of Class_Communicator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-sep-10 Expression company is undefined on line 7, column 30 in Templates/Joomla/name.php.
 * @category Plugins
 * @license	GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of backlink.
 * 
 * Class_Communicator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Class_Communicator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Class_Communicator.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class BacklinkValidator extends Communicator{
    var $Linker;
    var $Link;
    var $_errors = array();
    private $_backlinks = array();
    public static $ERROR_WRONG_ANCHOR = '1';
    public static $ERROR_NOT_FOUND = '2';
    public static $ERROR_NO_FOLLOW = '3';
    
    public function __construct($Link, $Linker, $config="") {
        parent::__construct();
        $defaultConfig = array(
            'isNoFollowValid' => false,
            'isDebugging' => false,
            'isIgnoringAnchorCasing' => true
        );
        if(is_array($config)) {
            $defaultConfig = array_merge($config, $defaultConfig);
        }
        $this->Link = $Link;
        $this->Linker = $Linker;
        $this->_config = array_merge($defaultConfig, $this->_config);
        $this->setUrl($this->Linker->baseURL());
        $this->setPostVars($this->Linker->queryVars());
    }
    
    public function check(){
        $this->query();
        $this->_findBackLinks();
        return $this->isFound();
    }
    
    private function _findBackLinks(){
        $html = new DOMDocument();
        $html->recover = true;
        $html->strictErrorChecking = false;
        libxml_use_internal_errors(true);
        $html->loadHTML($this->getResponse());
        libxml_clear_errors();
        foreach($html->getElementsByTagName('a') as $link) {
            $actualHref = $link->getAttribute('href');
            $expectedHref = $this->Link->href;
            $actualText = $this->_config['isIgnoringAnchorCasing'] ? strtolower($link->nodeValue) : $link->nodeValue;
            $expectedText = $this->_config['isIgnoringAnchorCasing'] ? strtolower($this->Link->text) : $this->Link->text;
            if($actualHref == $expectedHref){
                if($actualText = $expectedText){
                    $backlink = new Link();
                    $backlink->href = $actualHref;
                    $backlink->text = $actualText;
                    $backlink->rel = $link->getAttribute('rel');
                    $backlink->alt = $link->getAttribute('alt');
                    if($this->_isValid($backlink)){
                        $this->_backlinks[] = $backlink;                
                    }
                }else{
                    $this->_errors[] = self::$ERROR_WRONG_ANCHOR;
                }
            }
        }
    }
    
    private function _isValid($link){
        $isNoFollowValid = $this->_config['isNoFollowValid'];
        if($link->isNoFollow() && !$isNoFollowValid){
            $this->_errors[] = self::$ERROR_NO_FOLLOW;
            return false;
        }
        return true;
    }
    
    public function isFound(){
        if(!empty($this->_backlinks)){
            return true;
        }
        return false;
    }
    
    public function getErrors(){
        return $this->_errors;
    }
}