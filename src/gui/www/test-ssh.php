<?php
ini_set('display_errors',1);error_reporting(E_ALL);
$htmlData = file_get_contents('http://insight.localhost/report.html');
$htmlData = preg_replace('#\<div id="round 0"\>(.+?)provided\<\/pre\>#s', "", $htmlData);
$htmlFile = fopen("/opt/gopath/src/github.com/hyperledger/caliper/src/gui/www/report1.html", "w");
fwrite($htmlFile, $htmlData);
fclose($htmlFile);
?>