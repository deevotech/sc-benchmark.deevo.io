<?php
ini_set('display_errors',1);error_reporting(E_ALL);
$connect = ssh2_connect('127.0.0.1', 22);
if (ssh2_auth_password($connect, 'datlv', 'tango12@')) {
    echo "<br>connect";
}
?>