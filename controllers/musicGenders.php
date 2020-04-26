<?php

require_once('include/DbConnect.php');

function musicGenders($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        // This endpoint collect all the music genders
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
        // This endpoint collect one music gender by idGender
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
        // This endpoint collects the festivals with a particular musical gender
        case 'get_festival_by_gender':
            $id_music_gender = json_decode(file_get_contents("php://input"), true);
            $id_music_gender = $id_music_gender['id_gender'];
            if(isset($id_music_gender)){
                $sql_festivals = "SELECT idFestival FROM fest_festival_music_gender WHERE idMusicGender = ". $id_music_gender;
                $result = $conn->query($sql_festivals);
                $resultArray = array();
                foreach($result as $results){
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
        // This endpoint collects the musical genders of a festival
        case 'get_gender_by_festival':
            $id_festival = $_GET['id_festival'];
            $sql_festival = "
                SELECT gender.name
                FROM fest_music_gender gender
                LEFT JOIN fest_festival_music_gender fest
                ON gender.id = fest.idMusicGender
                WHERE fest.idFestival = ". $id_festival;

            $result = $conn->query($sql_festival);
            $resultArray = array();
            foreach($result as $results){
                $resultArray[] = $results;
            }
            $genders = array(
                'musicGenders' => $resultArray
            );

            $data['results'] = $genders;
            $data['count'] = count($resultArray);
            break;
        default:
            break;
    }
    return $data;
}
