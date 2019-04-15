<?php
	$dbserver   = "localhost";
    $dbname     = "powercontrol";
    $dbuser     = "wandog";
    $dbpass     = "q0919155809";
    
    // Header
    if(!@mysql_connect($dbserver, $dbuser, $dbpass))
        die('Can not connection to sql.');
    
    mysql_query("SET NAMES utf8");
    
    if(!@mysql_select_db($dbname))
        die('Db table FAIL');
?>