<?php

include("db.php"); // Conectamos a la Base de Datos
include("functions.php"); // Funciones comunes
include("config.php");  // Configs
require_once("adLDAP.php"); // Incluimos la clase para manejar el ActiveDirectory
//error_reporting(1);
// Si se accedio a el portal por bloqueo de algun sitio en particular

if ($_GET['op'] == "block"){

    echo '<p><font face="Verdana,Tahoma,Arial,sans-serif" color="red"><h1>Acceso Denegado!</h1><br></font>';
    
    echo '<font face="Verdana,Tahoma,Arial,sans-serif" size="2" color="grey">'."Hola " . $_GET['clientuser'] . "! estas en la maquina " . $_GET['clientaddr'] . " No tienes permisos para navegar en " . $_GET['url'] . " por politicas de grupo " . $_GET['clientgroup'] . "</br> Si piensas que esto no es correcto, por favor comunicalo al administrador!<br>";

    logg("Block",$_GET['clientuser']. " Ip:".$_GET['clientaddr']. " Group:".$_GET['clientgroup']." Url:".$_GET['url']);
    exit;

}

// Iniciamos chequeando si esta logueado

checkLogged();


if ($_GET['op'] == "login") {

$username = mysql_escape_string($_POST['username']);
$password = mysql_escape_string($_POST['password']);
$time = mysql_escape_string($_POST['time']);

$cmd = Auth($username,$password);

if ($cmd) {

echo '<p><font face="Verdana,Tahoma,Arial,sans-serif" size="2" color="gray">Authorizado!</p>';
	createSession($username,$time);
        $op = system("sudo /usr/local/sbin/squid -k reconfigure"); //recargamos la config para que el squid borre las credenciales cacheadas
	logg("Info","Reload squid config and flush users caches!".$op);
	
}else {
    echo '<p><font face="Verdana,Tahoma,Arial,sans-serif" size="2" color="red">Password o Usuario incorrecto.</p></font>';
}

}

function Auth($username, $password){
	
	// Creamos conexion con el ActiveDirectory
	$adldap = new adLDAP();

	// Authenticamos el usuario
	if ($adldap->authenticate($username,$password)){
		return true; // Valido
	}else{
		return false; // Invalido
	}

}


// Crea la session en la base de datos

function createSession($username,$smin) {

    global $pfhook, $rooturl; 
    
    
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
    	      
		$cmd = exec("sudo ".$pfhook." add ".$ip." 2>&1"); // Agregamos la ip a la tabla, para que pueda navegar
		echo logg("Info","Session created to ".$username. " ".$ip. " expire ".readTime($end));
		
		logg("PF",$cmd); // Escribimos la el stderr y stdout en el log :)
		echo "La session fue creada con exito!";
	        echo "</br>Redirigiendo...";
	        sleep(2);
	        echo '<meta HTTP-EQUIV="REFRESH" content="2; url='.$rooturl.'">';

               return true;
     }
}




// Ya inicio session ?


function checkLogged(){

    global $pfhook, $rooturl;

$query = "SELECT * FROM `sessions`";
$result = mysql_query($query);
$num_rows = mysql_num_rows($result); //extraemos numero de filas

while ($row=mysql_fetch_array($result)) { // recoremos registros

    if ($row['ip'] == $_SERVER['REMOTE_ADDR']){ // verificamos si la ip es la misma que el cliente

	include("userpanel.html");  // Inlcuimos el panel de usuario
    

if ($_GET['op'] == "delself"){ // si opta por kill session

	$ip = mysql_escape_string($_SERVER['REMOTE_ADDR']);
	$result = mysql_query("DELETE FROM sessions WHERE ip='".$ip."'"); // borramos al session de la bd
	
	$result = mysql_query($query);

	if ($result) {
	     
    	    $cmd = exec("sudo ".$pfhook." delete ".$ip." 2>&1");
	    logg("PF",$cmd);
	    logg("Info",$row['username']." kill session! ".$ip);
	    echo "Session finalizada, redirigiendo....\n";
            echo '<meta HTTP-EQUIV="REFRESH" content="2; url='.$rooturl.'">';
	    
	}else {
	    
	    logg("Error",$row['username']." trying kill session ".mysql_error());
	    echo "Error al intentar borrar la session\n";
	}

    }

return true;

} 

}


if (empty($num_rows)){
   

}

include("login.html");
}




        


?> 
