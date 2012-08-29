<?php
/**
 * @version     $Id$
 * @copyright   Copyright 2011 Stilero AB. All rights re-served.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

//No direct access
defined('_JEXEC) or die;');

class Communicator {
    
    protected $config;
    protected $header;
    protected $isPost;
    protected $curlHandler;
    protected $url;
    protected $postVars;
    protected $response;
    protected $info;
    protected $cookieFile;
    const HTTP_STATUS_OK = '200';

    function __construct($url, $postVars, $config="") {
        $this->isPost = true;
        $this->url = $url;
        $this->postVars = $postVars;
        $this->config = 
            array(
                'curlUserAgent'         =>  'Communicator - www.stilero.com',
                'curlConnectTimeout'    =>  20,
                'curlTimeout'           =>  20,
                'curlReturnTransf'      =>  true,
                'curlSSLVerifyPeer'     =>  false,
                'curlFollowLocation'    =>  false,
                'curlProxy'             =>  false,
                'curlProxyPassword'     =>  false,
                'curlEncoding'          =>  false,
                'curlHeader'            =>  false,
                'curlHeaderOut'         =>  true,
                'curlUseCookies'        =>  true,
                'debug'                 =>  false,
                'eol'                   =>  "<br /><br />"
            );
        if(is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }
    
    public function query(){
        $this->curlHandler = curl_init(); 
        $this->_setupCurl();
        $this->response = curl_exec ($this->curlHandler);
        $this->info = curl_getinfo($this->curlHandler); 
        curl_close ($this->curlHandler);
        $this->_destroyCookieFile();
    }
    
    protected function _setupCurl(){
        $this->_initCurlSettings();
        $this->_initCurlPostMode();
        $this->_initCurlHeader();
        $this->_initCurlProxyPassword();
        $this->_initCookieFile();
    }
    
    private function _initCurlSettings(){
        curl_setopt_array(
            $this->curlHandler, 
            array(
                CURLOPT_URL             =>  $this->url,
                CURLOPT_USERAGENT       =>  $this->config['curlUserAgent'],
                CURLOPT_CONNECTTIMEOUT  =>  $this->config['curlConnectTimeout'],
                CURLOPT_TIMEOUT         =>  $this->config['curlTimeout'],
                CURLOPT_RETURNTRANSFER  =>  $this->config['curlReturnTransf'],
                CURLOPT_SSL_VERIFYPEER  =>  $this->config['curlSSLVerifyPeer'],
                CURLOPT_FOLLOWLOCATION  =>  $this->config['curlFollowLocation'],
                CURLOPT_PROXY           =>  $this->config['curlProxy'],
                CURLOPT_ENCODING        =>  $this->config['curlEncoding'],
                CURLOPT_HEADER          =>  $this->config['curlHeader'],
                CURLINFO_HEADER_OUT     =>  $this->config['curlHeaderOut']
            )
        );
    }
    
    private function _initCurlPostMode(){
        if($this->isPost){
            curl_setopt($this->curlHandler, CURLOPT_POST, $this->isPost);
            curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, http_build_query($this->postVars));
        }
    }
    
    private function _initCurlHeader(){
        if($this->config['curlHeader']){
            $this->buildHTTPHeader;
            curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, $this->header);
        }
    }
    
    private function _buildHTTPHeader(){
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,"; 
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
        $header[] = "Cache-Control: max-age=0"; 
        $header[] = "Connection: keep-alive"; 
        $header[] = "Keep-Alive: 300"; 
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; 
        $header[] = "Accept-Language: en-us,en;q=0.5"; 
        $header[] = "Pragma: ";  
        $this->header = $header;
    }
    
    private function _initCurlProxyPassword(){
        if ($this->config['curlProxyPassword'] !== false) {
            curl_setopt($this->curlHandler, CURLOPT_PROXYUSERPWD, $this->config['curl_proxyuserpwd']);
        } 
    }
    
    private function _initCookieFile(){
        if(!$this->config['curlUseCookies']){
            return;
        }
        try {
            $this->cookieFile = tempnam(DS."tmp", "cookies");
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        if (!$this->cookieFile){
            return;
        }
        curl_setopt($this->curlHandler, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->curlHandler, CURLOPT_COOKIEJAR, $this->cookieFile);
    }
    
    private function _destroyCookieFile(){
        if($this->cookieFile != "" && $this->config['curlUseCookies']){
            unlink($this->cookieFile);
        }
    }
    
    public function getResponse(){
        return $this->response;
    }
    
    public function getInfo(){
        return $this->info;
    }
    
    public function getInfoHTTPCode(){
        return $this->info['http_code'];
    }
    
    public function isOK(){
        if ($this->info['http_code'] == self::HTTP_STATUS_OK) {
            return true;
        }else{
            return false;
        }
    }
}