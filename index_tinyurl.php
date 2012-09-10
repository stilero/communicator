<?php
define('_JEXEC', 1);
require_once 'communicator.php';
require_once 'tinyurl.php';
$tinyurl = new TinyUrl('http://salubrious.se/index.php?page=shop.product_details&flypage=flypage.tpl&product_id=37&category_id=67&option=com_virtuemart&Itemid=2');
print '<a href="'.$tinyurl->shorten().'" target="_blank">'.$tinyurl->shorten().'</a>';
?>

