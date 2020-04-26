<?php

require_once('Config.php');

// This function makes connection to the data base
function connect(){
    try {
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("ERROR -> No se pudo conectar con el servidor.");
        $conn->set_charset("utf8");
        return $conn;
    } catch (Exception $exception) {
        exit($exception->getMessage());
    }
}
