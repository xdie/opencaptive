<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "maindb";

$dbsock = mysql_connect($dbhost, $dbuser, $dbpass);

if(!$dbsock) {
        die("Cannot connect. " . mysql_error());
}

$dbselect = mysql_select_db($dbname);

if(!$dbselect) {
        die("Cannot select database " . mysql_error());
}




?>
