<?php
require_once('./db_connect.php');
require_once('./components/errorlog.php');

try{
     $fetch_course_stmt = $pdo->query("SELECT * FROM course_table");
     $courses = $fetch_course_stmt->fetchAll(PDO::FETCH_OBJ);
}catch(PDOException $e){
     errorLog($e);
}

$result;
foreach($courses as $course){
     $result = $result . "<option value='$course->course_title'>$course->course_title</option>";
}
echo $result;
?>