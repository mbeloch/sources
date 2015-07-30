<?php
session_start();
require '../vendor/autoload.php';
require 'facebook-functions.php';


if (isset($_POST['photos'])){
    //$photos = json_decode($_POST['photos']);
    $data = array('response' => 'Thank you for feed me!');
    header('Content-Type: application/json');
    echo json_encode($data);
}else {
    $data = array('response' => 'photos are not set');
    header('Content-Type: application/json');
    echo json_encode($data);
}
