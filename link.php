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
 * This file is part of link.
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

class Link{
    var $text;
    var $href;
    var $name;
    var $target;
    var $rel;
    var $alt;
    private $_parsedUrl;
    

    public function __construct($href="", $text="", $name="", $target="", $rel="") {
        $this->href = $href;
        $this->text = $text;
        $this->name = $name;
        $this->target = $target;
        $this->rel = $rel;
    }
    
    public function isNoFollow(){
        if(strtolower($this->rel) == 'nofollow'){
            return true;
        }
        return false;
    }
    
    public function baseURL(){
        if(!isset($this->_parsedUrl)){
            $this->_parsedUrl = parse_url($this->href);
        }
        $scheme = isset($this->_parsedUrl['scheme']) ? $this->_parsedUrl['scheme'] : '';
        $host = isset($this->_parsedUrl['host']) ? $this->_parsedUrl['host'] : '';
        $path = isset($this->_parsedUrl['path']) ? $this->_parsedUrl['path'] : '';
        return $scheme.'://'.$host.$path;
    }
    
    public function queryVars(){
        $query = isset($this->_parsedUrl['query']) ? $this->_parsedUrl['query'] : '';
        $vars = array();
        if($query != ''){
            parse_str($query, $vars);
        }
        return $vars;
    }
    
    public function __get($name) {
        return $name;
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
}
