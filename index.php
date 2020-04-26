<?php

require_once('controllers/festivals.php');
require_once('controllers/users.php');
require_once('controllers/profiles.php');
require_once('controllers/musicGenders.php');
require_once('controllers/images.php');
require_once('include/DbConnect.php');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    //    http_response_code(200);
    $path = $_GET['url'];
    $task = $_GET['task'];

    switch($path){
        case 'festivals':
            $result = festivals($task, connect());
            print_r(json_encode($result));
            break;
        case 'users':
            $result = users($task, connect());
            print_r(json_encode($result));
            break;
        case 'profiles':
            $result = profiles($task, connect());
            print_r(json_encode($result));
            break;
        case 'musicGenders':
            $result = musicGenders($task, connect());
            print_r(json_encode($result));
            break;
        case 'images':
            $result = images($task, connect());
            print_r(json_encode($result));
            break;
        default:
            break;
    }

}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // http_response_code(200);
    $path = $_GET['url'];
    $task = $_GET['task'];

    switch($path){
        case 'festivals':
            $result = festivals($task, connect());
            print_r(json_encode($result));
            break;
        case 'users':
            $result = users($task, connect());
            print_r(json_encode($result));
            break;
        case 'profiles':
            $result = profiles($task, connect());
            print_r(json_encode($result));
            break;
        case 'musicGenders':
            $result = musicGenders($task, connect());
            print_r(json_encode($result));
            break;
        case 'images':
            $result = images($task, connect());
            print_r(json_encode($result));
            break;
        default:
            break;
    }

}else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    // http_response_code(200);
    $path = $_GET['url'];
    $task = $_GET['task'];

    switch($path){
        case 'festivals':
            $result = festivals($task, connect());
            print_r(json_encode($result));
            break;
        case 'users':
            $result = users($task, connect());
            print_r(json_encode($result));
            break;
        case 'profiles':
            $result = profiles($task, connect());
            print_r(json_encode($result));
            break;
        case 'musicGenders':
            $result = musicGenders($task, connect());
            print_r(json_encode($result));
            break;
        case 'images':
            $result = images($task, connect());
            print_r(json_encode($result));
            break;
        default:
            break;
    }

}else{
    http_response_code(405); // método no permitido
}
