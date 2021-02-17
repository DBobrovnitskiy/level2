<?php

header('Access-Control-Allow-Origin: http://test2.local');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Access-Control-Allow-Credentials: true');

run();

/**
 * starts script execution
 */
function run()
{
    session_start();
    $post = getPostArray();
    echo getInsertID($post);
}

/**
 * Checks if the request is executable
 *
 * @return mixed
 */
function getPostArray()
{
    $post = json_decode(file_get_contents('php://input'), true);
    if (!isset($_SESSION['login'], $_SESSION['pass'], $post['text'])) {
        exit();
    }
    return $post;
}

/**
 * performs operations and returns the result
 *
 * @param $post - operation parameters
 * @return string - result
 */
function getInsertID($post): string
{
    include 'Database.php';
    $id = Database::addItem($_SESSION['login'], $post['text']);
    return $id == true ? '{"id":' . $id . '}' : '{"ok":false}';
}

