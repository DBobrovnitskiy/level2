<?php

header('Access-Control-Allow-Origin: http://test2.local');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Access-Control-Allow-Credentials: true');

run();

/**
 * starts script execution
 */
function run(){
    session_start();
    $post = json_decode(file_get_contents('php://input'),true);
    if(isCorrectPostAndSession($post)){
        echo response($post);
    }
}

/**
 * Checks if the request is executable
 *
 * @param $post - operation parameters
 * @return bool - true or false
 */
function isCorrectPostAndSession($post): bool
{
    return $post && isset($_SESSION['login'], $_SESSION['pass'], $post['id']);
}

/**
 * performs operations and returns the result
 *
 * @param $post - operation parameters
 * @return string - result
 */
function response($post): string
{
    include 'Database.php';
    if(Database::deleteItem($_SESSION['login'],$post['id'])){
        return '{"ok":true}';
    }
    return '{"ok":false}';
}



