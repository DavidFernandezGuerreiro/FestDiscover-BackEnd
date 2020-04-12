<?php

require_once('include/DbConnect.php');

function festivals($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        case 'get_all_festivals':
            $sql_all_festivals = 'SELECT * FROM fest_festivals';

            $result = $conn->query($sql_all_festivals);
            $resultArray = array();
            foreach($result as $results){
                $resultArray[] = $results;
            }

            $data['results'] = $resultArray;
            $data['count'] = count($resultArray);
            break;
        case 'get_festival':
            if(isset($_GET['id_festival'])) {
                $id_festival = $_GET['id_festival'];

                $sql_festival = 'SELECT * FROM fest_festivals WHERE id = ' . $id_festival;

                $result = $conn->query($sql_festival);
                $resultArray = array();
                foreach ($result as $results) {
                    $resultArray[] = $results;
                }

                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            }else{
                $data['error'] = 'Error. Faltan parámentros. No se ha enviado el id del festival.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        default:
            break;
    }
    return $data;
}
