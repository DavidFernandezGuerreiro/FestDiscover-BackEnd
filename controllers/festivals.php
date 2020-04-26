<?php

require_once('include/DbConnect.php');

function festivals($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    switch ($task) {
        // This endpoint collect all the festivals
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
        // This endpoint collect one festival by idFestival
        case 'get_festival':
            if(isset($_GET['id_festival'])) {
                $id_festival = $_GET['id_festival'];

                $sql_festival = 'SELECT * FROM fest_festivals WHERE id = ' . $id_festival;
                $music_genders = musicGenders('get_gender_by_festival', $conn);

                $result = $conn->query($sql_festival);
                $resultArray = array();
                foreach ($result as $results) {
                    $resultArray = array_merge($results, $music_genders['results']);
                }

                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            }else{
                $data['error'] = 'Error. Faltan parámentros. No se ha enviado el id del festival.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        // This endpoint collects the festivals filtering by country, musical gender, min and max price
        case 'get_festivals_by_filter':
            $filters = json_decode(file_get_contents("php://input"), true);
            $country = isset($filters['country']) ? $filters['country'] : '';
            $min_price = isset($filters['min_price']) ? $filters['min_price'] : '';
            $max_price = isset($filters['max_price']) ? $filters['max_price'] : '';

            $result_festivals = musicGenders('get_festival_by_gender', $conn);
            $id_festivals = $result_festivals['results'];

            if($country != '' && $min_price != '' && $max_price != ''){
                $resultArray = array();
                foreach($id_festivals as $val) {
                    $id_festival = intval($val['idFestival']);
                    $sql_festival = "
                    SELECT * FROM fest_festivals 
                    WHERE id = " . $id_festival . " AND country = '" . $country . "' AND price >= " . $min_price . " AND price <= " . $max_price;
                    $result = $conn->query($sql_festival);

                    foreach($result as $results){
                        array_push($resultArray, $results);
                    }
                }
                $data['results'] = $resultArray;
                $data['count'] = count($resultArray);
            }else{
                $data['error'] = 'Error. Faltan parámentros. Debe introducir todos los campos.';
                $data['results'] = null;
                $data['count'] = 0;
            }
            break;
        // This endpoint create a festival
        case 'create_festival':
            $festival = json_decode(file_get_contents("php://input"), true);
            $sql_id_role = $sql_user = 'SELECT idRole FROM fest_users WHERE idProfile = ' . $festival['idProfile'];
            $result = $conn->query($sql_id_role);
            $resultArray = array();
            foreach ($result as $results) {
                $resultArray[] = $results;
            }
            $idRole = $resultArray[0]['idRole'];

            if($idRole == '3' || $idRole == '1') {
                $sql_create = "
                INSERT INTO fest_festivals (name, description, country, location, initDate, endDate, linkTickets, price, idProfile)
                    VALUES ('". $festival['name'] ."', '". $festival['description'] ."', '". $festival['country'] ."', '". $festival['location'] ."', 
                    '". $festival['initDate'] ."', '". $festival['endDate'] ."', '". $festival['linkTickets'] ."', ". $festival['price'] .", ". $festival['idProfile'] .")
                ";

                $result = $conn->query($sql_create);
                if($result == true){
                    $data['message']['festival'] = 'El festival se ha creado correctamente.';

                    $sql_festival = 'SELECT * FROM fest_festivals WHERE idProfile = ' . $festival['idProfile'];
                    $result_festival = $conn->query($sql_festival);
                    $resultFestival = array();
                    foreach ($result_festival as $results) {
                        $resultFestival = $results;
                    }

                    foreach($festival['musicGenders']as $gender) {
                        $sql_create_festival_music_gender = "
                            INSERT INTO fest_festival_music_gender (idFestival, idMusicGender)
                            VALUES ('". $resultFestival['id'] ."', '". $gender ."')
                        ";
                        $result_gender = $conn->query($sql_create_festival_music_gender);
                        if($result_gender == true){
                            $data['message']['music_genders'] = 'Los géneros musicales se han creado correctamente.';
                        }else{
                            $data['message']['music_genders'] = 'Los géneros musicales no se han creado.';
                        }
                    }
                    $data['results'] = $result;
                    $data['count'] = count($result);
                }else{
                    $data['message'] = 'Error. El festival no se ha creado.';
                }
            }else{
                $data['message'] = 'No tiene permisos para crear un festival.';
                $data['results'] = 0;
                $data['count'] = 0;
            }

            break;
        default:
            break;
    }
    return $data;
}
