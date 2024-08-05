<?php

require_once('./components/errorlog.php');

$dsn = "mysql:host=localhost;dbname=project_db";
$db_password = "";
$db_user = "root";

try{
     $pdo = new PDO($dsn, $db_user, $db_password);
}catch(PDOException $e){
     errorLog($e);
}

?>
