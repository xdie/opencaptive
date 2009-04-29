<?php

/****************************************************************
La funcion de este script, es borrar las sessiones, cuando
su tiempo halla terminado en la Bd y pf
*****************************************************************/


include("db.php"); // Incluimos la BD 
include("functions.php"); // Funciones comunes
include("config.php"); // Configuracion


system("pfctl -F all; pfctl -f ".$pfile); // Purgamos el pf y cargamos la config

// Loop Principal 

while(true) {


$query = "SELECT * FROM `sessions`"; 
$result = mysql_query($query);
$num_rows = mysql_num_rows($result); // Extraemos numero de filas


	    if (empty($num_rows)) {
     
	    	//echo "No records\n";
	    	//logg("Info","No records in DB :-[");
	    }
    

    while ($row=mysql_fetch_array($result)) { // recoremos registros

		$now = time(); // Hora actual
    
	    if ($now > $row["end"]) { // si la hora actual es mayor que el tiempo de expiracion
	
			    $arraytime = array(); // Reset al array de para la hora
			    $op = mysql_query("DELETE FROM sessions WHERE ip='".$row['ip']."'"); // Borramos la ip que expiro

		    if (!$op) {
				echo "Error deleting".$row['ip']."\n";
			        logg("Error","Error trying delete ".$row['ip']);
		      
		    	    } else {
				    $cmd = exec($pfhook." delete ".$row['ip']." 2>&1"); // borramos la ip de la tabla redproxy
				    logg("PF",$cmd);
				    logg("Info","Session in ".$row['ip']. " expired! deleting...");		
				    echo "Session con la ip ".$row['ip']." fue borrada de la Base de Datos\n";
		    	    }
    	    } 
    } 

showCurrent();  
sleep(5);       
}

// Mostrar sessiones activas

function showCurrent() {

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


