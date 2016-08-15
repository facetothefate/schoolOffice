<?php
require "config.php";
function getDB()
{
    global $config;
    $dbhost = $config['host'];
    $dbuser = $config['user'];
    $dbpass = $config['passwd'];
    $dbname = $config['db'];

    $mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
    $dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$dbConnection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    return $dbConnection;
}
?>
