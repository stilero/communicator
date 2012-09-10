<?php
define('_JEXEC', 1);
require_once '../communicator/communicator/communicator.php';
require_once 'backlinkchecker.php';
$urlToCheck = 'http://www.google.com';
$urlToCheck = 'http://www.stilero.com';
$backlinkToCheckFor = 'http://www.stilero.com/';
$anchor = 'Stilero Webdesign';
$config = array(
    'isIgnoringAnchorCasing' => true,
    'isDebugging' => TRUE
);
$backlinkchecker = new BacklinkChecker($urlToCheck, $backlinkToCheckFor, $anchor, $config);
print $backlinkchecker->validate();
//print $backlinkchecker->getResponse();
//print $backlinkchecker->getInfoHTTPCode();
?>
<pre><?php var_dump($backlinkchecker->getBacklinks());?></pre>

