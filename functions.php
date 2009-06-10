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

// Escrbir registro de sucessos

function logg($type,$string) {

    include("config.php"); // Cargamos la configuracion

$fd = fopen($logfile,"a");
$time = date("M d H:m:s");

$data = $time."\t".$type."  ".$string."\n";

fwrite($fd,$data);
fclose($fd);

}

// Envio de alertas por mail

function alertMail($subject,$mailmsg) {

error_reporting(1);

include("Mail.php");
include("config.php");

$recipients = $mailadmin;
$headers["From"] = $mailfrom;
$headers["To"] = $mailadmin;
$headers["Subject"] = $subject;

$mail_object =& Mail::factory("smtp", $smtpconf);
$mail_object->send($recipients, $headers, $mailmsg);

}
?>
