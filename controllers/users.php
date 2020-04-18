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

                $sql_user = "
                    INSERT INTO fest_users (name, email, password, idProfile)
                    VALUES ('" . $user['name'] . "', '" . $user['email'] . "', '" . $user['password'] . "', '" . $selectProfile['results'][0]['id'] . "')
                ";

                $result = $conn->query($sql_user);
                if ($result == true) {
                    $data['message'] = 'El usuario se ha creado correctamente.';
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
        default:
            break;
    }
    return $data;
}