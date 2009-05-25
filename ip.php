#!/usr/local/bin/php

<?php

    /*****************************************************************
     *Este script , es el famoso ACL Helper del Squid		     *
     *Falta revision no funciona del todo bien, el squid tira errores*
     *de lectura vacios, aun sigo esperando que me contesten. quien sabe :)			     *
     *****************************************************************/


include("functions.php"); 
include("config.php"); 

// Conectamos con la base de datos

$dbsock = mysql_connect($dbhost, $dbuser, $dbpass);

if(!$dbsock) {
        logg("BD",mysql_error());
        die("Cannot connect. " . mysql_error());
}

$dbselect = mysql_select_db($dbname);

if(!$dbselect) {
        logg("BD",mysql_error());
        die("Cannot select database " . mysql_error());
}

// Loop principal

while (!feof(STDIN)) {

        $line = trim(fgets(STDIN));
        $ip = rawurldecode($line);
	if ($ip == "\n"){
	fwrite(STDOUT, "ERR"."\n"); // no encontro nada, lo pateamos
	
	}else{
	checkIp($ip);	
	}
}


// Chekeamos que si la ip se encuentra en la base!

function checkIp($ip) {

$ip = mysql_escape_string($ip);
$query = "SELECT * FROM `sessions`";
$result = mysql_query($query);
$num_rows = mysql_num_rows($result);

while ($row=mysql_fetch_array($result)) {

if ($row['ip'] == $ip) {
    fwrite(STDOUT, "OK user=".$row['username']."\n"); // si la encuentra devolvemos el username
    logg("Info","Ok returned from iphelper to: ".$row['username']);
    return true;
}  


}

fwrite(STDOUT, "ERR"."\n"); // no encontro nada, lo pateamos
    //logg("Error","ERR returned from iphelper ");

}

?>

