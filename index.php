<?php

include("db.php"); // Conectamos a la Base de Datos
include("functions.php"); // Funciones comunes
include("config.php");


echo  '<style type="text/css">';
include("style.css");
echo '</style>'; // Stylesheet


if ($_GET['op'] == "block"){

    echo "<h1>Usted no tiene permisos!</h1>\n";
    echo "Hola " . $_GET['clientuser'] . "! estas en la maquina " . $_GET['clientaddr'] . " No tienes permisos para navegar en " . $_GET['url'] . " por politicas de grupo " . $_GET['clientgroup'] . "</br> Si piensas que esto no es correcto, por favor comunicalo al administrador!<br>";

    logg("Block",$_GET['clientuser']. " Ip:".$_GET['clientaddr']. " Group:".$_GET['clientgroup']." Url:".$_GET['url']);
    exit;

}


if ($_GET['op'] == "feedback"){

if ($_POST['comment'] == ""){
echo '<form name="form1" method="post" action="?op=feedback">
     <label><br> Por favor escribanos su comentario.<br> 
      <br><textarea name="comment" cols="50" rows="10">Comentario</textarea>
     </label><p><label></label><input type="submit" name="Submit" value="Enviar"></p></form>';
exit; // Terminamos el script

} else {

  $cuerpo = "FeedBack\n";
  $cuerpo .= "Comentario: " . $_POST["comment"] . "\n";
  $cuerpo .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

 // Mando el mail
  mail("xdieamd@gmail.com","FeedBack OpenCaptive",$cuerpo); 
  echo "Su comentario fue enviado con exito!\n";

}

// FeedBack form

}

// Si es valido el usuario y password vamos authorizar creando la session
    
$username = mysql_escape_string($_POST['username']);
$time = mysql_escape_string($_POST['time']);

   if (empty($username) or empty($time)) {
	checkLogged();
	
	} else {
 
        createSession($username,$time);
        $op = system("sudo /usr/local/sbin/squid -k reconfigure"); //recargamos la config para que el squid borre las credenciales cacheadas
	logg("Info","Reload squid config and flush users caches!".$op);
	}


// Crea la session en la base de datos

function createSession($username,$smin) {

    $arraytime = array();
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $mac = "00:0d:87:a6:ec:96"; // mac ficticia
    $start = time(); 		// Marcamos hora inicial
    $end = $smin * 60 + $start; //Pasamos los minutos a segundos y seteamos cuando va expirar
    $query = "INSERT INTO `sessions` (username, ip, mac, start, end) VALUES ('$username','$ip', '$mac', '$start', '$end')"; 

    $result = mysql_query($query);
    
    if (!$result) {

    	    echo "fallo al crear la session".mysql_error()."\n";
	    logg("Error","Problems trying create user session in DB".mysql_error());
    	    return false;
        } else {
        
	      echo logg("Info","Session created to ".$username. " ".$ip. " expire ".readTime($end));
              echo "La session fue creada con exito!";
	      echo "</br>Redirigiendo...";
	      sleep(5);
	      echo '<meta HTTP-EQUIV="REFRESH" content="2; url=http://192.168.35.118:8080">';

               return true;
     }
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
	    logg("Info",$row['username']." kill session! ".$ip);
	    echo "Session finalizada, redirigiendo....\n";
	    echo '<meta HTTP-EQUIV="REFRESH" content="5; url=http://192.168.35.118:8080">';
	}else {
	    
	    logg("Error",$row['username']." trying kill session ".mysql_error());
	    echo "Error al intentar borrar la session\n";
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

