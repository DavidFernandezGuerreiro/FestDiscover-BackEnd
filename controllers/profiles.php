<?php

require_once('include/DbConnect.php');

function profiles($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        case 'get_all_profiles':
            $sql_all_profiles = 'SELECT * FROM fest_profiles';

            $result = $conn->query($sql_all_profiles);
            $resultArray = array();
            foreach($result as $results){
                $resultArray[] = $results;
            }

            $data['results'] = $resultArray;
            $data['count'] = count($resultArray);
            break;
        case 'get_profile':
            if(isset($_GET['id_profile'])) {
                $id_profile = $_GET['id_profile'];

                $sql_profile = 'SELECT * FROM fest_profiles WHERE id = ' . $id_profile;

                $result = $conn->query($sql_profile);
                $resultArray = array();
                foreach ($result as $results) {
                    $resultArray[] = $results;
                }

                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            }else{
                $data['error'] = 'Error. Faltan parámentros. No se ha enviado el id del perfil.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        case 'get_profile_name':
            $profile = json_decode(file_get_contents("php://input"), true);

            $sql_profile = "SELECT * FROM fest_profiles WHERE name = '" . $profile['nameProfile'] . "'";

            $result = $conn->query($sql_profile);
            $resultArray = array();
            foreach ($result as $results) {
                $resultArray[] = $results;
            }

            $data['results'] = $resultArray;
            $data['count'] = count($resultArray);
            break;
        case 'create_profile':
            $profile = json_decode(file_get_contents("php://input"), true);

            $sql_profile = "
                INSERT INTO fest_profiles (name)
                VALUES ('".$profile['nameProfile']."')
            ";

            $result = $conn->query($sql_profile);
            if($result == true){
                $data['message'] = 'El perfil se ha creado correctamente.';
            }else{
                $data['message'] = 'Error. El perfil no se ha creado.';
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        default:
            break;
    }
    return $data;
}
