<?php

require_once('Config.php');

/*$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("ERROR -> No se pudo conectar con el servidor.");
$conn->set_charset("utf8");*/

function connect(){
    try {
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("ERROR -> No se pudo conectar con el servidor.");
        $conn->set_charset("utf8");
        return $conn;
    } catch (Exception $exception) {
        exit($exception->getMessage());
    }
}

function getResponse($sql, $conn = null){
    if(!$conn)global $conn;
    $result = $conn->query($sql);
    $resultArray = array();
    foreach($result as $results){
        $resultArray[] = $results;
    }
    return $resultArray;
}
