<?php

header('Access-Control-Allow-Origin: http://test2.local');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Access-Control-Allow-Credentials: true');

run();

function run()
{
    session_start();
    $post = json_decode(file_get_contents('php://input'), true);
    if (isCorrectPostAndSesioin($post)) {
        echo response($post);
    }
}

function isCorrectPostAndSesioin($post)
{
    return (
        isset($_SESSION['login'], $_SESSION['pass']) &&
        isset($post['id'], $post['text'], $post['checked']) &&
        $post['checked']
    );
}

function response($post)
{
    include 'Database.php';
    if (Database::changeItem($_SESSION['login'], $post['id'], $post['text'])) {
        return '{"ok":true}';
    }
    header('HTTP/1.1 400 Bad Request');
    return '{"ok":false}';
}
