<?php

include("config.php");

$dbsock = mysql_connect($dbhost, $dbuser, $dbpass);

if(!$dbsock) {
        die("Cannot connect. " . mysql_error());
}

$dbselect = mysql_select_db($dbname);

if(!$dbselect) {
        die("Cannot select database " . mysql_error());
}




?>
