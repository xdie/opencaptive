<?php
$dn = "OU=People,OU=staff,DN=ad,DN=wjgilmore,DN=com";
$attributes = array("displayname", "l");
$filter = "(cn=*)";
$ad = ldap_connect("ldap://")
         or die("Couldn't connect to AD!");
  
    ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);

    $bd = ldap_bind($ad,"userÂ@dedicado.com","secret")
          or die("Couldn't bind to AD!");

    $result = ldap_search($ad, $dn, $filter, $attributes);

    $entries = ldap_get_entries($ad, $result);

    for ($i=0; $i<$entries["count"]; $i++)
    {
        echo $entries[$i]["displayname"]
             [0]."(".$entries[$i]["l"][0].")<br />";
    }

    ldap_unbind($ad);

?>

