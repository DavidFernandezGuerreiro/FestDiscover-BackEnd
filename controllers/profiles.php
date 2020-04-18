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
        case 'update_profile':
            $profile = json_decode(file_get_contents("php://input"), true);

            $name = isset($profile['name']) ? $profile['name'] : '';
            $city = isset($profile['city']) ? $profile['city'] : '';
            $province = isset($profile['province']) ? $profile['province'] : '';
            $country = isset($profile['country']) ? $profile['country'] : '';
            $numberPhone = isset($profile['numberPhone']) ? $profile['numberPhone'] : '';
            $image = isset($profile['image']) ? $profile['image'] : '';

            if(isset($profile['dateBirth'])){
                $sql_update = "
                UPDATE fest_profiles 
                SET name = '". $name ."', city = '". $city ."', province = '". $province ."', country = '". $country ."', dateBirth = '". $profile['dateBirth'] ."',
                 numberPhone = '". $numberPhone ."', image = '". $image ."'
                WHERE id = ". $profile['id'];
            } else {
                $sql_update = "
                UPDATE fest_profiles 
                SET name = '". $name ."', city = '". $city ."', province = '". $province ."', country = '". $country ."', dateBirth = null,
                 numberPhone = '". $numberPhone ."', image = '". $image ."'
                WHERE id = ". $profile['id'];
            }

            $result = $conn->query($sql_update);
            if($result == true){
                $data['message'] = 'El perfil se ha actualizado correctamente.';
            }else{
                $data['message'] = 'Error. El perfil no se ha actualizado.';
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        default:
            break;
    }
    return $data;
}
