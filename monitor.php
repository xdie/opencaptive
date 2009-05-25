<?php
include("functions.php");
// Este es un monitor para detectar cuadno falla squidguard y nos deja canilla libre de internet

$today = date("Y/m/d");

$filter = "grep " . $today . " /var/squid/logs/cache.log |  grep 'Cannot run' /var/squid/logs/cache.log | grep squidGuard | tail -n 1 ";


$cmd = shell_exec($filter);
//var_dump($cmd);

if (!empty($cmd)){

logg("ERROR","Problemas con el redirector SquidGuard! revisar configuracion!");

$slog = "tail /var/squidguard/squidGuard.log";
$cmd = shell_exec($slog);

alertMail("Alerta! fallo el redirector","El redirector squidGuard no esta funcionando por algun tema de configuracion o permisos!\n\n".$cmd);


} else {

logg("Monitor","Probablemente el squidguard este funcionando de maravilla");

}
?>
