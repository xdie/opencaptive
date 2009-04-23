<?php

include("db.php"); // Incluimos la BD
include("functions.php"); // Funciones comunes
include("config.php"); // Configuracion


system("pfctl -F all; pfctl -f /etc/pf.conf"); // Purgamos el pf y lo reseteamos


// Loop Principal 

while(true) {

$out = system("pfctl -F nat"); // Flusheamos las tablas de NAT

$fd = fopen($natfile,"w"); // Abrimos archivo dinamico

if (!$fd) {
    logg("Error","Error opening nat file".$natfile);
}

$query = "SELECT * FROM `sessions`"; 
$result = mysql_query($query);
$num_rows = mysql_num_rows($result); // Extraemos numero de filas


if (empty($num_rows)) {
     
	echo "No records\n";
	logg("Info","No records in DB :-[");
    }
    

while ($row=mysql_fetch_array($result)) { // recoremos registros

$now = time(); //Hora actual

    if ($now > $row["end"]){ // si la hora actual es mayor que el tiempo de expiracion
	
	$arraytime = array(); // Reset al array de para la hora

	$rule = "rdr on bge0 inet proto tcp from ".$row['ip']." to any port 80 -> 192.168.35.118 port 8080"."\n"; // redirect
	
	$op = mysql_query("DELETE FROM sessions WHERE ip='".$row['ip']."'"); // Borramos la ip que expiro


	    if (!$op) {
		    echo "error deleting".$row['ip']."\n";
		    logg("Error","Error trying delete ".$row['ip']);
		}else{
		    logg("Info","Session in ".$row['ip']. " expired! deleting...");		
		    echo "\nSession con la ip ".$row['ip']." fue borrada de la Base de Datos\n";
	    }
	    
	    
      } else { // si esta con vida aun, lo agregamos al PF y redireccionasmo al proxy
	
	    $rule = "rdr on bge0 inet proto tcp from ".$row['ip']." to any port 80 -> 127.0.0.1 port 3128"."\n"; // Regla para redireccion al proxy permitiendo la navegar
	
	}

	fwrite($fd,$rule); // Escribimos la regla en el fichero
} 

$default = "rdr on bge0 inet proto tcp from any to any port 80 -> 192.168.35.118 port 8080"."\n"; //regla por defecto para que la ip que no sea reconocida fuerze a loguear

fwrite($fd,$default); // Escribimos de nuevo


$get = file_get_contents($natfile); // Obtengo el contenido

if ($get == "") {
    echo "NAT File is empty\n";
    logg("Error","Nat file is empty");
}else {

system("pfctl -f ".$natfile); // Cargo las reglas al pf

    echo "Loading rule from NAT file\n";
}

showCurrent();  // Muestro las sessiones
sleep(5);       // esperamos 5 seg para loopear
}

// Mostrar sessiones activas

function showCurrent(){

system("clear");
echo "*************************************************************************\n";
echo "* OpenCaptive\t\t\t\t\t Hora Actual: ".readTime(time())."\t*\n";
echo "*************************************************************************\n";
$query = "SELECT * FROM `sessions`"; 
$result = mysql_query($query);

    while ($row=mysql_fetch_array($result)) { // recoremos registros

	echo "Username:".$row['username']."\tIP:".$row['ip']."    Start:".readTime($row['start'])."  End:".readTime($row['end'])."\n";

    }

}


?>


