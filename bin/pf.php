#!/usr/local/bin/php
<?php
// Este script es un hook para manejar el pf, por lo tanto corre como root
// Aqui va el codigo para filtrar el referer para que no sea ejecutado por cualquiera :)


pf($argv['1'],$argv['2']);

function pf($action,$ip) {

    $op = passthru("pfctl -t redproxy -T".$action." ".$ip);


}

?>
