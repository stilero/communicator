<?php
/**
 * Backlinkchecker validates backlinks on a webpage
 *
 * @version  1.3
 * @author Daniel Eliasson - joomla at stilero.com
 * @copyright  (C) 2012-aug-31 Stilero Webdesign http://www.stilero.com
 * @category Classes
 * @license GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of BacklinkChecker.
 * 
 * BacklinkChecker is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * BacklinkChecker is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with BacklinkChecker.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class BacklinkChecker extends Communicator{
    private $_urlToCheck;
    private $_postVarsFromUrlToCheck;
    private $_anchorTextToCheckFor;
    private $_backlinkToCheckFor;
    private $_isNoFollowValid;
    private $_links;
    private $_backlink;
    private $_backlinks = array();
    private $_isDebugging;
    private $_isIgnoringAnchorCasing;
    
    public function __construct($urlToCheck, $backlinkToCheckFor, $anchorTextToCheckFor="", $config = "") {
        $this->_urlToCheck = $urlToCheck;
        $this->_processURL();
        parent::__construct($this->_urlToCheck, $this->_postVarsFromUrlToCheck, $config);
        $defaultConfig = array(
            'isNoFollowValid' => false,
            'isDebugging' => false,
            'isIgnoringAnchorCasing' => true
        );
        if(is_array($config)) {
            $defaultConfig = array_merge($config, $defaultConfig);
        }
        $this->_config = array_merge($defaultConfig, $this->_config);
        $this->_anchorTextToCheckFor = $anchorTextToCheckFor;
        $this->_backlinkToCheckFor = $backlinkToCheckFor;
        $this->_isNoFollowValid = $this->_config['isNoFollowValid'];
        $this->_isDebugging = $this->_config['isDebugging'];
        $this->_isIgnoringAnchorCasing = $this->_config['isIgnoringAnchorCasing'];
    }
    
    private function _processURL(){
        $parsedURL = parse_url($this->_urlToCheck);
        $urlScheme = isset($parsedURL['scheme']) ? $parsedURL['scheme'] : '';
        $urlHost = isset($parsedURL['host']) ? $parsedURL['host'] : '';
        $urlPath = isset($parsedURL['path']) ? $parsedURL['path'] : '';
        $urlQuery = isset($parsedURL['query']) ? $parsedURL['query'] : '';
        $this->_urlToCheck = $urlScheme.'://'.$urlHost.$urlPath;
        if($urlQuery != ''){
            parse_str($urlQuery, $this->_postVarsFromUrlToCheck);
        }
    }
    
    public function validate(){
        $this->query();
        $this->_fetchLinks();
        return $this->hasFoundValidBacklink();
    }
    
    private function _isBacklinkValid(){
        if($this->_isBacklinkViolatingNoFollow()){
            if($this->_isDebugging) print "backlink violating no follow<br/>";
            return false;
        }
        if(!$this->_isAnchorCorrect()){
            if($this->_isDebugging) print "backlink wrong anchor<br/>";
            return false;
        }
        if($this->_hasPageNoIndexMeta()){
            if($this->_isDebugging) print "Page has no robot index<br/>";
            return false;
        }
        return TRUE;
    }
    
    private function _isBacklinkViolatingNoFollow(){
        if($this->_isNoFollowValid){
            return false;
        }
        return $this->_hasBacklinkNoFollow();
    }
    
    private function _hasBacklinkNoFollow(){
        $backlinkRel = strtolower($this->_backlink['rel']);
        if($backlinkRel == 'nofollow'){
            return true;
        }
        return FALSE;
    }
    
    private function _isAnchorCorrect(){
        $backlinkAnchor = $this->_isIgnoringAnchorCasing ? strtolower($this->_backlink['anchortext']) : $this->_backlink['anchortext'];
        $anchorToLookFor = $this->_isIgnoringAnchorCasing ? strtolower($this->_anchorTextToCheckFor) : $this->_anchorTextToCheckFor;
        if($backlinkAnchor == $anchorToLookFor){
            return true;
        }
        if($this->_isDebugging) print "Anchor > Expected: ".$anchorToLookFor." - Acutal: ".$backlinkAnchor."<br/>";
        return false;
    }
    
    public function hasFoundValidBacklink(){
        if(!empty($this->_backlinks)){
            return true;
        }
        return false;
    }
    
    private function _hasPageNoIndexMeta(){
        $html = new DOMDocument();
        $html->recover = true;
        $html->strictErrorChecking = false;
        libxml_use_internal_errors(true);
        $html->loadHTML($this->getResponse());
        libxml_clear_errors();
        foreach($html->getElementsByTagName('meta') as $metatag) {
                $metaname = strtolower($metatag->getAttribute('name'));
                $metacontent = strtolower($metatag->getAttribute('content'));
                if($metaname == 'robots'){
                    if(strpos($metacontent, 'noindex') || strpos($metacontent, 'nofollow')){
                        return true;
                        break;
                    }
                }
        }
        return FALSE;
    }
    
    private function _fetchLinks(){
        $html = new DOMDocument();
        $html->recover = true;
        $html->strictErrorChecking = false;
        libxml_use_internal_errors(true);
        $html->loadHTML($this->getResponse());
        libxml_clear_errors();
        $links = array();
        foreach($html->getElementsByTagName('a') as $link) {
            $linkurl = array(
                'href' => $link->getAttribute('href'),
                'alt' => $link->getAttribute('alt'),
                'rel' => $link->getAttribute('rel'),
                'anchortext' => $link->nodeValue
            );
            $links[] = $linkurl;
            if($link->getAttribute('href') == $this->_backlinkToCheckFor){
                $this->_backlink = $linkurl;
                if($this->_isBacklinkValid()){
                    $this->_backlinks[] = $linkurl;
                }
            }
        }
        $this->_links = $links;
    }
    
    public function getLinks(){
        return $this->_links;
    }
    
    public function getBacklinks(){
        return $this->_backlinks;
    }
}