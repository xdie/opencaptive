
<?php

include("db.php"); // Incluimos la BD

system("pfctl -F all; pfctl -f /etc/pf.conf"); // Purgamos el pf y lo reseteamos


// Loop Principal 

while(true) {

system("pfctl -F nat"); // Flusheamos las tablas de NAT

$fd = fopen("/etc/nat.conf","w"); // Abrimos archivo dinamico

$query = "SELECT * FROM `sessions`"; 
$result = mysql_query($query);
$num_rows = mysql_num_rows($result); // Extraemos numero de filas


if (empty($num_rows)) {
     
	echo "No records\n";
    }
    



while ($row=mysql_fetch_array($result)) { // recoremos registros

$now = time(); //Hora actual

    if ($now > $row["end"]){ // si la hora actual es mayor que el tiempo de expiracion
	
	$arraytime = array(); // Reset al array de para la hora

	//$rule = "rdr on bge0 inet proto tcp from ".$row['ip']." to any port 80 -> 192.168.35.118 port 8080"."\n"; // redirect
	
	$op = mysql_query("DELETE FROM sessions WHERE ip='".$row['ip']."'"); // Borramos la ip que expiro
	
	    if (!$op) {
		    echo "error deleting".$row['ip']."\n";
		}else{
		    echo "\nSession con la ip ".$row['ip']." fue borrada de la Base de Datos\n";
	    }
	    
	    
      } else { // si esta con vida aun, lo agregamos al PF y redireccionasmo al proxy
	
	    $rule = "rdr on bge0 inet proto tcp from ".$row['ip']." to any port 80 -> 127.0.0.1 port 3128"."\n"; // Regla para redireccion al proxy permitiendo la navegar
	
	}

	fwrite($fd,$rule); // Escribimos la regla en el fichero

} 

$default = "rdr on bge0 inet proto tcp from any to any port 80 -> 192.168.35.118 port 8080"."\n"; //regla por defecto para que la ip que no sea reconocida fuerze a loguear

fwrite($fd,$default); // Escribimos de nuevo


$get = file_get_contents("/etc/nat.conf"); // Obtengo el contenido

if ($get == "") {
    echo "NAT File is empty\n";
}else {

system("pfctl -f /etc/nat.conf"); // Cargo las reglas al pf

    echo "Loading rule from NAT file\n";
}

showCurrent();  // Muestro las sessiones
sleep(5);       // esperamos 5 seg para loopear
}

// Mostrar sessiones activas

function showCurrent(){

system("clear");
echo "Hora Actual: ".readTime(time())."\n";
$query = "SELECT * FROM `sessions`"; 
$result = mysql_query($query);

    while ($row=mysql_fetch_array($result)) { // recoremos registros

	echo "Username:".$row['username']."\tIP:".$row['ip']."    Start:".readTime($row['start'])."    End:".readTime($row['end'])."\n";

    }

}


// Funcion para leer la hora correctamente

function readTime($time) {

    $arraytime = localtime($time, true);

    if ($arraytime['tm_min'] < 10 ) {
        $min = "0".$arraytime['tm_min'];
    }else {
	$min = $arraytime['tm_min'];
    }
    if ($arraytime['tm_sec'] < 10 ) {
        $sec = "0".$arraytime['tm_sec'];
    }else {
	$sec = $arraytime['tm_sec'];
    }
    
	return $arraytime['tm_hour'].":".$min.":".$sec;
}


?>


