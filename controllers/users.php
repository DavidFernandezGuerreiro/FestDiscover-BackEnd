<?php

require_once('include/DbConnect.php');
require_once('profiles.php');

function users($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        // This endpoint collect all the users
        case 'get_all_users':
            $sql_all_users = 'SELECT * FROM fest_users';

            $result = $conn->query($sql_all_users);
            $resultArray = array();
            foreach ($result as $results) {
                $resultArray[] = $results;
            }

            $data['results'] = $resultArray;
            $data['count'] = count($resultArray);
            break;
        // This endpoint collect one user by idUser
        case 'get_user':
            if (isset($_GET['id_user'])) {
                $id_user = $_GET['id_user'];

                $sql_user = 'SELECT * FROM fest_users WHERE id = ' . $id_user;

                $result = $conn->query($sql_user);
                $resultArray = array();
                foreach ($result as $results) {
                    $resultArray[] = $results;
                }

                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            } else {
                $data['error'] = 'Error. Faltan parámentros. No se ha enviado el id del usuario.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        // This endpoint create a user
        case 'create_user':
            $user = json_decode(file_get_contents("php://input"), true);

            $boolCreate = false;
            $allProfiles = profiles('get_all_profiles', $conn);
            foreach ($allProfiles['results'] as $results) {
                if ($results['name'] == $user['nameProfile']) {
                    $boolCreate = false;
                    break;
                } else {
                    $boolCreate = true;
                }
            }

            if ($boolCreate == true) {
                $resultProfile = profiles('create_profile', $conn);
                $selectProfile = profiles('get_profile_name', $conn);

                $pass_hash = password_hash($user['password'], PASSWORD_DEFAULT);
                $sql_user = "
                    INSERT INTO fest_users (name, email, password, idProfile, idRole)
                    VALUES ('" . $user['name'] . "', '" . $user['email'] . "', '" . $pass_hash . "', '" . $selectProfile['results'][0]['id'] . "', '" . $user['idRole'] . "')
                ";

                $result = $conn->query($sql_user);
                if ($result == true) {
                    $sql_id_user = "SELECT id FROM fest_users WHERE idProfile = " . $selectProfile['results'][0]['id'];
                    $result = $conn->query($sql_id_user);
                    $result_user = array();
                    foreach ($result as $results) {
                        $result_user[] = $results; // $results['id']
                    }

                    $sql_profile = "UPDATE fest_profiles SET idUser = " . intval($result_user[0]['id']) . " WHERE id = " . $selectProfile['results'][0]['id'];
                    $result = $conn->query($sql_profile);
                    if ($result == true) {
                        $data['message']['user_id_profile'] = 'El idUser se ha añadido correctamente en el perfil.';
                    }

                    $data['message']['user'] = 'El usuario se ha creado correctamente.';
                    print_r(json_encode($resultProfile));
                } else {
                    $data['message']['user'] = 'Error. El usuario no se ha creado.';
                }
                $data['results'] = $result;
                $data['count'] = count($result);
            } else {
                $data['error'] = 'Error. Ya existe un perfil con ese nombre de usuario.';
            }
            break;
        // This endpoint allows a user to login
        case 'user_login':
            $credentials = json_decode(file_get_contents("php://input"), true);

            $result_profile = profiles('get_profile_name', $conn);
            $profile = $result_profile['results'];
            $sql_user = 'SELECT * FROM fest_users WHERE id = ' . $profile[0]['idUser'];

            $result = $conn->query($sql_user);
            foreach ($result as $results) {
                if (password_verify($credentials['password'], $results['password'])) {
                    $data['message'] = 'La contraseña es válida.';
                    $data['results'] = true;
                    $data['count'] = 0;
                } else {
                    $data['message'] = 'La contraseña es incorrecta.';
                    $data['results'] = false;
                    $data['count'] = 0;
                }
            }
            break;
        // This endpoint allows you to update a user
        case 'update_user':
            $user = json_decode(file_get_contents("php://input"), true);

            $name = isset($user['name']) ? $user['name'] : '';
            $password = isset($user['password']) ? $user['password'] : '';

            $sql_update = "
                UPDATE fest_users
                SET name = '" . $name . "', password = '" . $password . "'
                WHERE id = " . $user['idUserUpdate'];

            $result = $conn->query($sql_update);
            if ($result == true) {
                $data['message'] = 'El usuario se ha actualizado correctamente.';
            } else {
                $data['message'] = 'Error. El usuario no se ha actualizado.';
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        // This endpoint allows you to delete an entire user.
        // - If it is a user with the role of promoter, it deletes the profile and the festivals created.
        // - If you are a user with the role user, delete the profile, musical tastes and favorite festivals.
        case 'delete_user':
            $user = json_decode(file_get_contents("php://input"), true);

            $sql_user = 'SELECT * FROM fest_users WHERE id = ' . $user['id'];
            $result = $conn->query($sql_user);
            foreach ($result as $results) {
                $resultArray = $results;
                $sql_delete = "DELETE FROM fest_users WHERE id = " . $user['id'];

                $result = $conn->query($sql_delete);
                if ($result == true) {
                    if ($resultArray['idRole'] === '3') {

                        $sql_festival = 'SELECT * FROM fest_festivals WHERE idProfile = ' . $resultArray['idProfile'];
                        $result_festival = $conn->query($sql_festival);
                        foreach ($result_festival as $results) {
                            $resultFestival = $results;

                            $sql_delete_festival = "DELETE FROM fest_festival_music_gender WHERE idFestival = " . $resultFestival['id'];
                            $result = $conn->query($sql_delete_festival);
                            if ($result == true) {
                                $data['message']['music_genders'] = 'Los géneros musicales de los festivales se han eliminado correctamente.';
                            } else {
                                $data['message']['music_genders'] = 'Error. Los géneros musicales de los festivales no se han eliminado.';
                            }

                            $sql_delete_festival = "DELETE FROM fest_festivals WHERE id = " . $resultFestival['id'];
                            $result = $conn->query($sql_delete_festival);
                            if ($result == true) {
                                $data['message']['festivals'] = 'Los festivales del usuario se han eliminado correctamente.';
                            } else {
                                $data['message']['festivals'] = 'Error. Los festivales del usuario no se han eliminado.';
                            }
                        }
                    }

                    if ($resultArray['idRole'] === '2') {
                        $sql_delete_gender = "DELETE FROM fest_profile_music_gender WHERE idProfile = " . $resultArray['idProfile'];
                        $result_gender = $conn->query($sql_delete_gender);
                        if ($result_gender == true) {
                            $data['message']['music_gender'] = 'Los géneros musicales del perfil se han eliminado correctamente.';
                        } else {
                            $data['message']['music_gender'] = 'Error. Los géneros musicales del perfil no se han eliminado.';
                        }
                        $sql_delete_favorite = "DELETE FROM fest_favorite_festivals WHERE idProfile = " . $resultArray['idProfile'];
                        $result_favorite = $conn->query($sql_delete_favorite);
                        if ($result_favorite == true) {
                            $data['message']['favorite'] = 'El festival se ha eliminado de favoritos.';
                        } else {
                            $data['message']['favorite'] = 'Error. El festival no se ha podido eliminar.';
                        }
                    }

                    $sql_delete_profile = "DELETE FROM fest_profiles WHERE id = " . $resultArray['idProfile'];
                    $result = $conn->query($sql_delete_profile);
                    if ($result == true) {
                        $data['message']['profile'] = 'El perfil del usuario se ha eliminado correctamente.';
                    } else {
                        $data['message']['profile'] = 'El perfil del usuario no se ha eliminado.';
                    }

                    $data['message']['user'] = 'El usuario se ha eliminado correctamente.';
                } else {
                    $data['message']['user'] = 'Error. El usuario no se ha eliminado.';
                }
            }
            $data['results'] = $result;
            $data['count'] = count($result);
            break;
        default:
            break;
    }
    return $data;
}
