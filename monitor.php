<?php

// Este es un monitor para detectar cuadno falla squidguard y nos deja canilla libre de internet

include("functions.php");



$today = date("Y/m/d"); // fecha actual para buscar en el log de hoy

$filter = "grep " . $today . " /var/squid/logs/cache.log |  grep 'Cannot run' /var/squid/logs/cache.log | grep squidGuard | tail -n 1 "; // filtro re loco para cambiar a las necesidades

$cmd = shell_exec($filter); 
//var_dump($cmd);

if (!empty($cmd)){

$slog = "tail /var/squidguard/squidGuard.log"; // sacamos un fragmentillo del log

$cmd = shell_exec($slog);

if (ereg("emergency", $cmd)) { // buscamos si el error esta en el final del log, en caso de que falle mas de una vez por dia xD

logg("ERROR","Problemas con el redirector SquidGuard! funcionando en modo de emergencia!");

alertMail("Alerta! fallo el redirector","El redirector squidGuard no esta funcionando !\n\n".$cmd);

} else {

logg("Info","Probablemente el squidguard este funcionando de maravilla");
}

}
?>
