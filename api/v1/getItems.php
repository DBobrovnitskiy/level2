<?php

header('Access-Control-Allow-Origin: http://test2.local');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Access-Control-Allow-Credentials: true');

session_start();

/**
 * Returns the list as json format
 */
$post = json_decode(file_get_contents('php://input'),true);
if (isset($_SESSION['login'], $_SESSION['pass'])){
    include 'Database.php';
    echo json_encode(array('items' => Database::getItems($_SESSION['login'])));
}




