<?php

require_once('include/DbConnect.php');

function profiles($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        // This endpoint collect all the profiles
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
        // This endpoint collect one profile by idProfile
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
        // This endpoint collect one profile by unique name
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
        // This endpoint create a profile
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
        // This endpoint allows you to update a profile
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
                $data['message']['profile'] = 'El perfil se ha actualizado correctamente.';

                if(array_key_exists('musicGenders', $profile)) {
                    $sql_delete_festival = "DELETE FROM fest_profile_music_gender WHERE idProfile = " . $profile['id'];
                    $result = $conn->query($sql_delete_festival);

                    foreach ($profile['musicGenders'] as $gender) {
                        $sql_create_profile_music_gender = "
                            INSERT INTO fest_profile_music_gender (idProfile, idMusicGender)
                            VALUES ('" . $profile['id'] . "', '" . $gender . "')
                        ";
                        $result_gender_profile = $conn->query($sql_create_profile_music_gender);
                        if ($result_gender_profile == true) {
                            $data['message']['music_genders'] = 'Los géneros musicales se han actualizado correctamente.';
                        } else {
                            $data['message']['music_genders'] = 'Error. Los géneros musicales no se han actualizado.';
                        }
                    }
                }

            }else{
                $data['message'] = 'Error. El perfil no se ha actualizado.';
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        // This endpoint adds a festival to favorites
        case 'add_favorite_festival':
            $favorite = json_decode(file_get_contents("php://input"), true);

            $sql_favorite = "
                INSERT INTO fest_favorite_festivals (idProfile, idFestival)
                VALUES ('" . $favorite['idProfile'] . "', '" . $favorite['idFestival'] . "')    
            ";
            $result = $conn->query($sql_favorite);
            if ($result == true) {
                $data['message'] = 'El festival se ha añadido a favoritos.';
            } else {
                $data['message'] = 'Error. El festival no se ha podido añadir.';
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        // This endpoint eliminates a favorite festival
        case 'delete_favorite_festival':
            $favorite = json_decode(file_get_contents("php://input"), true);

            $sql_delete = "DELETE FROM fest_favorite_festivals WHERE idProfile = " . $favorite['idProfile'] . " AND idFestival = " . $favorite['idFestival'];
            $result = $conn->query($sql_delete);
            if ($result == true) {
                $data['message'] = 'El festival se ha eliminado de favoritos.';
            } else {
                $data['message'] = 'Error. El festival no se ha podido eliminar.';
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        default:
            break;
    }
    return $data;
}
