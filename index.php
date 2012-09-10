<?php
define('_JEXEC', 1);
require_once 'communicator.php';
require_once 'backlink.php';
require_once 'link.php';
$link = new Link('http://www.stilero.com/', 'Stilero webdesign');
$linker = new Link('http://www.stilero.com');
$backlink = new BacklinkValidator($link, $linker);
print $backlink->check();
?>

