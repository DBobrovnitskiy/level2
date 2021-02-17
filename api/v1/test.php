<?php

try {
    $base = new PDO('mysql:host=localhost;dbname=test_base', 'root', 'denis123');
    echo 'YES';
    $table = 'user';
    $text = '456';
    $id = 4;
    $prepare = $base->prepare("UPDATE `$table` SET `text` = :text WHERE `$table`.`id` = :id;");
    $kloun = 'k';


    $prepare->bindParam(':text', $text);
    $prepare->bindParam(':id', $id);
    $prepare->bindParam(1, $return_value, PDO::PARAM_STR, 4000);

    if($prepare->execute()){
        echo $return_value;
    }else{
        echo 'no';
    }
}catch (PDOException $e){
    echo 'NO';
}

