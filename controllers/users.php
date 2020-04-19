<?php

require_once('include/DbConnect.php');
require_once('profiles.php');

function users($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        case 'get_all_users':
            $sql_all_users = 'SELECT * FROM fest_users';

            $result = $conn->query($sql_all_users);
            $resultArray = array();
            foreach($result as $results){
                $resultArray[] = $results;
            }

            $data['results'] = $resultArray;
            $data['count'] = count($resultArray);
            break;
        case 'get_user':
            if(isset($_GET['id_user'])) {
                $id_user = $_GET['id_user'];

                $sql_user = 'SELECT * FROM fest_users WHERE id = ' . $id_user;

                $result = $conn->query($sql_user);
                $resultArray = array();
                foreach ($result as $results) {
                    $resultArray[] = $results;
                }

                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            }else{
                $data['error'] = 'Error. Faltan parámentros. No se ha enviado el id del usuario.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        case 'create_user':
            $user = json_decode(file_get_contents("php://input"), true);

            $boolCreate = false;
            $allProfiles = profiles('get_all_profiles', $conn);
            foreach ($allProfiles['results'] as $results) {
                if($results['name'] == $user['nameProfile']){
                    $boolCreate = false;
                    break;
                }else{
                    $boolCreate = true;
                }
            }

            if($boolCreate == true) {
                $resultProfile = profiles('create_profile', $conn);
                $selectProfile = profiles('get_profile_name', $conn);

                $pass_hash = password_hash($user['password'], PASSWORD_DEFAULT);
                $sql_user = "
                    INSERT INTO fest_users (name, email, password, idProfile, idRole)
                    VALUES ('" . $user['name'] . "', '" . $user['email'] . "', '" . $pass_hash . "', '" . $selectProfile['results'][0]['id'] . "', '". $user['idRole'] ."')
                ";

                $result = $conn->query($sql_user);
                if ($result == true) {
                    $sql_id_user = "SELECT id FROM fest_users WHERE idProfile = ". $selectProfile['results'][0]['id'];
                    $result = $conn->query($sql_id_user);
                    $result_user = array();
                    foreach ($result as $results) {
                        $result_user[] = $results; // $results['id']
                    }

                    $sql_profile = "UPDATE fest_profiles SET idUser = ". intval($result_user[0]['id']) ." WHERE id = ". $selectProfile['results'][0]['id'];
                    $result = $conn->query($sql_profile);
                    if ($result == true) {
                        $data['message'][0] = 'El idUser se ha añadido correctamente en el perfil.';
                    }

                    $data['message'][1] = 'El usuario se ha creado correctamente.';
                    print_r(json_encode($resultProfile));
                } else {
                    $data['message'] = 'Error. El usuario no se ha creado.';
                }
                $data['results'] = $result;
                $data['count'] = count($result);
            }else{
                $data['error'] = 'Error. Ya existe un perfil con ese nombre de usuario.';
            }
            break;
        case 'user_login':
            $credentials = json_decode(file_get_contents("php://input"), true);

            $result_profile = profiles('get_profile_name', $conn);
            $profile = $result_profile['results'];
            $sql_user = 'SELECT * FROM fest_users WHERE id = '. $profile[0]['idUser'];

            $result = $conn->query($sql_user);
            foreach ($result as $results) {
                if(password_verify($credentials['password'], $results['password'])){
                    $data['message'] = 'La contraseña es válida.';
                    $data['results'] = true;
                    $data['count'] = 0;
                }else{
                    $data['message'] = 'La contraseña es incorrecta.';
                    $data['results'] = false;
                    $data['count'] = 0;
                }
            }
            break;
        default:
            break;
    }
    return $data;
}
