<?php

require_once('include/DbConnect.php');

function images($task, $conn){

    $data = array(
        'results' => [],
        'count' => 0
    );

    $dir_subida = 'imagesUpload';

    switch ($task) {
        // This endpoint allows you to upload images
        case 'upload_images':
            $profile = $_POST['idProfile'];
            $mydirectory = $dir_subida.'/profile-'.$profile;
            if (!file_exists($mydirectory)) {
                mkdir($mydirectory, 0777, true);
            }

            $file_name = $_FILES['upload_image']['name'];
            $file_type = $_FILES['upload_image']['type'];
            $file = $mydirectory.'/'.$file_name;

            if (strpos($file_type, "jpeg") || strpos($file_type, "png")) {
                if(move_uploaded_file($_FILES['upload_image']['tmp_name'], $file)){
                    $data['message'] = 'La imágen se ha subido correctamente.';
                    $data['results'] = 1;
                    $data['count'] = 1;
                }else{
                    $data['message'] = 'Error. La imágen no se ha subido.';
                    $data['results'] = 0;
                    $data['count'] = 0;
                }
            }else{
                $data['message'] = 'Error. La extensión del archivo es incorrecta. (png, jpeg)';
                $data['results'] = 0;
                $data['count'] = 0;
            }
            break;
        default:
            break;
    }
    return $data;
}
