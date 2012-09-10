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
 * This file is part of googleurlshortener.
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

class GoogleUrlShortener extends Communicator{
   private $_longUrl;
    public $url;
    
    public function __construct($url, $config="") {
        parent::__construct();
        $defaultConfig = array(
            'shortUrlApi' => 'https://www.googleapis.com/urlshortener/v1/url'
        );
        if(isset($config) && !empty($config)){
            $defaultConfig = array_merge($defaultConfig, $config);
        }
        $this->_config = array_merge($defaultConfig, $this->_config);
        $this->_longUrl = $url;
    }
    
     public function shorten(){
         
        $post = array('longUrl' => $this->_longUrl);
        $this->setPostVars($post);
        $this->setUrl($this->_config['shortUrlApi']);
        //$this->_prepareHeader();
        $this->query();
        return $this->getResponse();
    }
    
    private function _prepareHeader(){
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,"; 
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
        $header[] = 'Content-Type: application/json';
        $header[] = "Pragma: "; 
        $this->setHeader($header);
    }
}
