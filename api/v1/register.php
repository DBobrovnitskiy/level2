<?php
header('Access-Control-Allow-Origin: http://test2.local');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");


$post = json_decode(file_get_contents('php://input'), true);

echo isCorrectResult($post) ? '{"ok":true}' : '{"ok":false}';

/**
 * performs an operation with the database
 * and then returns a boolean value confirming
 * the success of the operation
 *
 * @param $post - operation parameters
 * @return bool - true or false
 */
function isCorrectResult(&$post): bool
{
    include 'Database.php';
    return (
        $post &&
        isset($post['login'], $post['pass']) &&
        Database::register($post['login'], $post['pass']));
}