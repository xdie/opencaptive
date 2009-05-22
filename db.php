<?php

/* Este archivo solo conecta con la base de datos :)*/

include("config.php");
include("functions.php");

$dbsock = mysql_connect($dbhost, $dbuser, $dbpass);

if(!$dbsock) {
	logg("DB",mysql_error());
        die("Cannot connect. " . mysql_error());
}

$dbselect = mysql_select_db($dbname);

if(!$dbselect) {
	logg("DB",mysql_error());
        die("Cannot select database " . mysql_error());
}




?>
