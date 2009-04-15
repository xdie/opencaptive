#!/usr/local/bin/php

<?php
include("db.php");

// loop principal
while (!feof(STDIN)) {
        $line = trim(fgets(STDIN));
        $ip = rawurldecode($line);
	checkIp($ip);	
    
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
    return true;
}  


}
fwrite(STDOUT, "ERR"."\n"); // no encontro nada, lo pateamos


sleep(2);
}

?>

