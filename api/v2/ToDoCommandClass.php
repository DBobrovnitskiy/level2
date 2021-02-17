<?php


/**
 * Class Database
 * this class contains the basic commands for working with the site,
 * namely:
 * - show Todo_list;
 * - Add a entry
 * - delete entry
 * - change entry
 * Also contains commands for login and logout, and registration
 */
class ToDoCommandClass
{
    private const ERROR = 'HTTP/1.1 500 Internal Server Error';
    private const BAD_REQUEST = 'HTTP/1.1 400 Bad Request';
    private const TRUE = '{"ok":true}';
    private const FALSE = '{"ok":false}';
    private const USER_TABLE = 'users';

    /**
     * Returns the todo_list as json format
     *
     * @return string - todo_list
     */
    public static function getItems(): string
    {
        $base = self::runSqlCommand("SELECT * FROM " . $_SESSION['login']);
        return self::createTable($base);
    }

    /**
     * Forms an array containing a list and then
     * formats it into json format
     *
     * @param $rows - list rows
     * @return string - json list
     */
    private static function createTable($rows): string
    {
        $array = array('items' => array());
        while ($row = $rows->fetch()) {
            $array['items'][] = array('id' => $row[0], 'text' => $row[1], 'checked' => true);
        }
        return json_encode($array);
    }

    /**
     * Inserts an entry in the list,
     * the index entries returned
     *
     * @return string - id item
     */
    public static function addItem(): string
    {
        $command = "INSERT INTO " . $_SESSION['login'] . " (id,text)VALUES(NULL,:text);";
        $bindParam = (array(':text' => $_POST['text']));
        $id = self::runSqlCommand($command, $bindParam, true);
        return $id == true ? '{"id":' . $id . '}' : self::FALSE;
    }

    /**
     * Changes the entry in the list
     *
     * @return string - "ok":true or "ok":false
     */
    public static function changeItem(): string
    {
        $command = "UPDATE " . $_SESSION['login'] . " SET text = :text WHERE " . $_SESSION['login'] . ".id = :id;";
        $bindParam = (array(':text' => $_POST['text'], ':id' => $_POST['id']));
        return self::getResult(self::runSqlCommand($command, $bindParam));
    }

    /**
     * Removes an entry from the list
     *
     * @return string - "ok":true or "ok":false
     */
    public static function deleteItem()
    {
        $command = "DELETE FROM " . $_SESSION['login'] . " WHERE " . $_SESSION['login'] . ".id = :id;";
        $bindParam = (array(':id' => $_POST['id']));
        return self::getResult(self::runSqlCommand($command, $bindParam));
    }

    /**
     * Checks for the presence and compliance
     * of the username and password
     *
     * @return string - "ok":true or "ok":false
     */
    public static function login(): string
    {
        $command = "SELECT * FROM users WHERE login LIKE ? AND pass LIKE ?;";
        $bindParam = array("%" . $_POST['login'] . "%", "%" . $_POST['pass'] . "%");
        $row = self::runSqlCommand($command, $bindParam)->fetch();
        if ($row[0] === $_POST['login'] && $row[1] === $_POST['pass']) {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['pass'] = $_POST['pass'];
            return self::TRUE;
        }
        self::logout();
        return self::FALSE;
    }

    /**
     * Adds a new user, and a new table is
     * created specifically for this user
     *
     * @return string  - "ok":true or "ok":false
     */
    public static function register(): string
    {
        $addUser = "INSERT INTO " . self::USER_TABLE . " (login, pass) VALUES (?, ?);";
        $addTable = (
            "CREATE TABLE " . $_POST['login'] . " (id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , text TEXT" .
            " CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB;"
        );
        $hasAUserBeenAdded = self::runSqlCommand($addUser, array($_POST['login'], $_POST['pass']));
        $wasTheTableCreated = self::runSqlCommand($addTable);
        return self::getResult($hasAUserBeenAdded && $wasTheTableCreated);
    }

    /**
     * Returns the result as json
     *
     * @param $isTrue - boolean
     * @return string  - "ok":true or "ok":false
     */
    private static function getResult($isTrue): string
    {
        return $isTrue ? self::TRUE : self::FALSE;
    }

    /**
     * It connects to the database and then
     * performs operations with the database.
     * To do this, use the PDO object
     *
     * @param $command - performed operation
     * @param array $bindParam - prepared values as an array
     * @param false $isInsert - boolean, when used, returns the id of the entry
     * @return false|PDOStatement|
     */
    private static function runSqlCommand($command, $bindParam = [], $isInsert = false)
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=test_base', 'root', '');
            $prepare = $pdo->prepare($command);
            if ($prepare->execute($bindParam)) {
                return $isInsert ? $pdo->lastInsertId() : $prepare;
            }
            header(self::BAD_REQUEST);
            return false;
        } catch (PDOException $e) {
            header(self::ERROR);
            return false;
        }
    }

    /**
     * Breaks the session to exit your personal account
     *
     * @return string  - "ok":true
     */
    public static function logout(): string
    {
        $_SESSION = array();
        $_COOKIE = array();
        return self::TRUE;
    }
}