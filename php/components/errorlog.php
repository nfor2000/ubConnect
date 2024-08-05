<?php
function errorLog(PDOException $e){
     echo "An error occurred: ".$e->getMessage();
}
?>