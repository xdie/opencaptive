<?php

include("db.php"); // Conectamos a la Base de Datos

if ($_GET['op']== "block"){
    
    echo "<h1>Usted no tiene permisos!</h1>\n";
    echo "Hola " . $_GET['clientuser'] . "! estas en la maquina " . $_GET['clientaddr'] . " No tienes permisos para navegar en " . $_GET['url'] . " por politicas de grupo " . $_GET['clientgroup'] . "</br> Si piensas que esto no es correcto, por favor comunicalo al administrador!<br>";
exit;

}

// Si es valido el usuario y password vamos authorizar creando la session
    
   $username = mysql_escape_string($_POST['username']);
   $time = mysql_escape_string($_POST['time']);

   if (empty($username) or empty($time)) {
	checkLogged();
   }else{
       createSession($username,$time);
    echo system("sudo /usr/local/sbin/squid -k reconfigure"); //recargamos la config para que el squid 

    }


function createSession($username,$smin) {

$arraytime = array();

    $ip = $_SERVER['REMOTE_ADDR'];
    $mac = "00:0d:87:a6:ec:96"; //mac ficticia
    $start = time(); // marcamos el inicio
     
    $end = $smin * 60 + $start; //pasamos los minutos a segundos y seteamos cuando va expirar
    $query = "INSERT INTO `sessions` (username, ip, mac, start, end) VALUES ('$username','$ip', '$mac', '$start', '$end')"; 

     $result = mysql_query($query);
     if (!$result) {

        echo "fallo al crear la session".mysql_error()."\n";
        return false;
          }else {
              
               echo "La session fue creada con exito!";
	       sleep(3);
	       echo "</br>Redirigiendo...";
	       sleep(4);
	       
	      echo '<meta HTTP-EQUIV="REFRESH" content="2; url=http://192.168.35.118:8080">';

               return true;
     }
}


function readTime($time) {
    $arraytime = localtime($time, true);
    if ($arraytime['tm_min'] < 10 ) {
    $min = "0".$arraytime['tm_min'];
    }else {
    $min = $arraytime['tm_min'];
    }
    return $arraytime['tm_hour'].":".$min;
}


// Ya inicio session ?


function checkLogged(){

$query = "SELECT * FROM `sessions`";
$result = mysql_query($query);
$num_rows = mysql_num_rows($result); //extraemos numero de filas

while ($row=mysql_fetch_array($result)) { // recoremos registros

    if ($row['ip'] == $_SERVER['REMOTE_ADDR']){ // verificamos si la ip es la misma que el cliente

	echo "Bienvenido ".$row['username']. " tu session empezo a las " .readTime($row['start']). " y finaliza a las " .  readTime($row['end'])."</br>";
	echo "Mientras tanto puedes optar por visitar los siguientes links</br></br>";
	echo "<a href=\"http://www.google.com.uy\">Google!</a></br>"; // Google link
	echo "<a href=\"http://www.elpais.com.uy\">Diario ElPais</a></br>"; // Diario link
	echo "<a href=\"login.php?op=delself\">Terminar mi session Ahora!</a></br></body>"; // Kill session


if ($_GET['op'] == "delself"){ // si opta por kill session

	$ip = mysql_escape_string($_SERVER['REMOTE_ADDR']);
	$result = mysql_query("DELETE FROM sessions WHERE ip='".$ip."'"); // borramos al session de la bd
	
	$result = mysql_query($query);

	if ($result) {
	
	    echo "Session finalizada, redirigiendo....\n";
	    echo '<meta HTTP-EQUIV="REFRESH" content="5; url=http://192.168.35.118:8080">';
	}else {
	    echo "Error al borrar la session". mysql_error();
	}

    }

return true;

} 

}


if (empty($num_rows)){
   
    include("login.html");

return false;

}

    include("login.html");

}



?>

