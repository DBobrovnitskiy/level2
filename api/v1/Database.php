<?php


class Database
{

    //Returns the todo_list as json format
    public static function getItems($userName)
    {
        $base = self::runSqlCommand("SELECT * FROM `$userName`", false);
        return $base ? self::createTable($base) : false;
    }

    // Forms an array containing a list and then
    // formats it into json format
    private static function createTable($rows): array
    {
        $array = array();
        while ($row = mysqli_fetch_row($rows)) {
            $array[] = array('id' => $row[0], 'text' => $row[1], 'checked' => true);
        }
        return $array;
    }

    // Inserts an entry in the list,
    // the index entries returned
    public static function addItem($userName, $text)
    {
        $command = "INSERT INTO `$userName` (`id`, `text`) VALUES (NULL, '$text');";
        return self::runSqlCommand($command, true);
    }

    // Changes the entry in the list
    public static function changeItem($userName, $id, $text)
    {
        $command = "UPDATE `$userName` SET `text` = '$text' WHERE `$userName`.`id` = $id;";
        return self::runSqlCommand($command, false);
    }

    // Removes an entry from the list
    public static function deleteItem($userName, $id)
    {
        $command = "DELETE FROM `$userName` WHERE `$userName`.`id` =  $id;";
        return self::runSqlCommand($command, false);
    }

    // Checks for the presence and compliance
    // of the username and password
    public static function login($login, $pass)
    {
        $command = "SELECT * FROM `users` WHERE `login` LIKE '$login' AND `pass` LIKE '$pass';";
        return mysqli_fetch_row(self::runSqlCommand($command, false));
    }

    // Adds a new user, and a new table is
    // created specifically for this user
    public static function register($login, $pass): bool
    {
        $addUser = "INSERT INTO `users` (`login`, `pass`) VALUES ('$login', '$pass');";
        $addTable = (
            "CREATE TABLE `$login` ( `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT , `text` TEXT" .
            " CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;"
        );
        return self::runSqlCommand($addUser, false) && self::runSqlCommand($addTable, false);
    }

    // It connects to the database and then
    // performs operations with the database.
    // To do this, use the "mesqli" object
    private static function runSqlCommand($command, $isInsert)
    {
        $base = new mysqli('localhost', 'root', '', 'test_base');
        if (mysqli_connect_errno()) {
            header('HTTP/1.1 500 Internal Server Error');
            return false;
        }
        $result = $base->query($command);
        if ($base->errno) {
            header('HTTP/1.1 400 Bad Request');
            return false;
        }
        $toReturn = $isInsert ? $base->insert_id : $result;
        $base->close();
        return $toReturn;
    }


}