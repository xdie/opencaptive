<?php

// Pasar la hora corriente a un formato legible

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

function logg($type,$string) {

include("config.php"); // Cargamos la configuracion

$fd = fopen($logfile,"a");

$data = $type." | ".date("d:m:Y h:m:s")." ".$string."\n";
fwrite($fd,$data);
fclose($fd);

}



?>
