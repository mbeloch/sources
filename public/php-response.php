<?php
session_start();
$data = array( 'html_url' => 'God', 'owner' => 'mrcaaas' );
header('Content-Type: application/json');
echo json_encode($data);