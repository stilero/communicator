<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HTMLHelper
 *
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 */
class HTMLGeneratorHelper {
    
    public static function htmlWithKnownLink($href, $linkText){
        $body = self::bodyWithRandomLinksAndKnownLink($href, $linkText);
        $html = self::htmlWrapper($body);
        return $html;
    }
    
    public static function htmlWithUnknownLinks(){
        $body = self::bodyWithRandomLinks();
        $html = self::htmlWrapper($body);
        return $html;
    }


    public static function htmlWrapper($body){
        $html = '<!DOCTYPE html><title>Testpage</title>';
        $html .= $body;
        $html .= '</html>';
        return $html;
    }
    
    public static function bodyWithRandomLinksAndKnownLink($href, $linkText, $nofollow = false){
        $nofollowTag = $nofollow ? ' rel="nofollow"' : '';
        $html = '<body>';
        $html .= '<div id="links">';
        $html .= '<ul>';
        $html .= self::buildRandomLinks(10);
        $html .= '<li><a href="'.$href.'"'.$nofollowTag.'>'.$linkText.'</a></li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</body>';
        return $html;
    }
    
    public static function bodyWithRandomLinks(){
        $html = '<body>';
        $html .= '<div id="links">';
        $html .= '<ul>';
        $html .= self::buildRandomLinks(10);
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</body>';
        return $html;
    }
    
    public static function buildRandomLinks($numOfLinks){
        $html = '';
        for($i=0;$i<$numOfLinks;$i++){
            $linktext = self::buildRandomString(10);
            $href = 'http://www.'.self::buildRandomString(10).'.com';
            $html .= '<li><a href="'.$href.'">'.$linktext.'</a></li>';
        }
        return $html;
    }
    
    public static function buildRandomString($length){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }
        return $str;
    }
    
}

?>
