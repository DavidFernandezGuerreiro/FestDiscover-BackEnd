<?php

require_once('include/DbConnect.php');

function musicGenders($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        case 'get_all_music_genders':
            $sql_all_genders = 'SELECT * FROM fest_music_gender';

            $result = $conn->query($sql_all_genders);
            $resultArray = array();
            foreach($result as $results){
                $resultArray[] = $results;
            }

            $data['results'] = $resultArray;
            $data['count'] = count($resultArray);
            break;
        case 'get_music_gender':
            if(isset($_GET['id_gender'])) {
                $id_gender = $_GET['id_gender'];

                $sql_gender = 'SELECT * FROM fest_music_gender WHERE id = ' . $id_gender;

                $result = $conn->query($sql_gender);
                $resultArray = array();
                foreach ($result as $results) {
                    $resultArray[] = $results;
                }

                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            }else{
                $data['error'] = 'Error. Faltan parámentros. No se ha enviado el id del género musical.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        default:
            break;
    }
    return $data;
}
