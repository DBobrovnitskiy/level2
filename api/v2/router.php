<?php
header('Access-Control-Allow-Origin: http://test3.local');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Access-Control-Allow-Credentials: true');

$a = new router();
echo $a->run();

/**
 * Class router
 * based on the request, selects and executed
 * one of the supported operating
 */
class router
{
    private const ACTION = 'action';
    private const REGISTER = 'register';
    private const LOGIN = 'login';
    private const LOGOUT = 'logout';
    private const ADD = 'addItems';
    private const DELETE = 'deleteItem';
    private const CHANGE = 'changeItem';
    private const GET_ITEMS = 'getItems';

    /**
     * router constructor.
     */
    public function __construct()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);
        include 'ToDoCommandClass.php';
        session_start();
    }

    /**
     * Starts router execution
     *
     * @return string
     */
    public function run(): string
    {
        if (!$this->itSupportsActions()) {
            header('HTTP/1.1 404 Not Found');
            return '';
        }
        if (isset($_SESSION[self::LOGIN], $_SESSION[self::LOGIN])) {
            return $this->anythingActions();
        }
        return $this->loginActions();
    }

    /**
     * Performs any of the available operations
     * uses query string to select an operation
     *
     * @return string - result of the performed operation
     */
    private function anythingActions(): string
    {
        $functionName = $_GET[self::ACTION];
        if ($_GET[self::ACTION] == self::REGISTER || self::GET_ITEMS || self::LOGOUT) {
            return ToDoCommandClass::$functionName();
        }
        return $_POST ? ToDoCommandClass::$functionName() : '';
    }

    /**
     * Performs only operations with login
     * uses query string to select an operation
     *
     * @return string - result of the performed operation
     */
    private function loginActions(): string
    {
        $functionName = $_GET[self::ACTION];
        if ($_GET[self::ACTION] == self::REGISTER || self::LOGIN) {
            return $_POST ? ToDoCommandClass::$functionName() : '';
        }
        return '';
    }

    /**
     * @return bool - Whether the request is a supported operation
     */
    private function itSupportsActions(): bool
    {
        return (
            $_GET[self::ACTION] == self::REGISTER || self::LOGIN || self::DELETE ||
            self::CHANGE || self::ADD || self::GET_ITEMS || self::LOGOUT
        );
    }
}