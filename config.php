<?php

// DB 
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "maindb";

// GENERAL
$rooturl = "http://192.168.35.118:8080";

// FW
$pfile = "/etc/pf.conf";
$pfhook = "/var/www/htdocs/opencaptive/bin/pf.php"; // Este gancho tiene que colocarse fuera del htdocs por seguridad!

// LOG
$logfile = "/tmp/log";

// MAIL CONFIG
$mailadmin = "rgliksberg@dedicado.com";
$mailfrom = "proxyserver@dedicado.com";

// MAIL SERVER CONFIG
$smtpconf["host"] = "mail.dedicado.com";
$smtpconf["port"] = "25";
$smtpconf["auth"] = true;
$smtpconf["username"] = "proxyserver";
$smtpconf["password"] = "proxy123456";
//$smtpconf["debug"] = TRUE;


?>
