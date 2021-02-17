<?php
header('Access-Control-Allow-Origin: http://test2.local');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Access-Control-Allow-Credentials: true');

session_start();

echo findUser(getPost());

/**
 * searches for a match of username and
 * password in the database and then returns the result
 *
 * @param $post - operation parameters
 * @return string - result
 */
function findUser($post): string
{
    include 'Database.php';
    $result = Database::login($post['login'], $post['pass']);
    if ($result && $result[0] === $post['login'] && $result[1] === $post['pass']) {
        return returnTrue($post);
    }
    return returnFalse();
}

/**
 * writes session parameters and returns the result
 *
 * @param $post  - operation parameters
 * @return string - result
 */
function returnTrue($post): string
{
    $_SESSION['login'] = $post['login'];
    $_SESSION['pass'] = $post['pass'];
    return '{"ok":true}';
}

/**
 * ends the session and returns the result
 *
 * @return string - result
 */
function returnFalse(): string
{
    $_SESSION = array();
    $_COOKIE = array();
    return '{"ok":false}';
}

/**
 * Checks if the request is executable
 *
 * @return mixed
 */
function getPost()
{
    $post = json_decode(file_get_contents('php://input'), true);
    if (!$post || !isset($post['login'], $post['pass'])) {
        echo returnFalse();
        exit();
    }
    return $post;
}