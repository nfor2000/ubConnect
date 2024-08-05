<?php
session_start();

require_once('./db_connect.php');
require_once('./components/errorlog.php');

if (isset($_GET['id'])) {
     $id = intval($_GET['id']);
}

$user_matricule = $_SESSION["matricule"];

try {
     $stmt1 = $pdo->prepare("SELECT * FROM occupy WHERE matricule = ? AND class_id = ?");
     $stmt1->execute([$user_matricule ,$id]);

     if($stmt1->rowCount() == 0){
          $stmt2 = $pdo->prepare("INSERT INTO occupy (class_id ,matricule) VALUES (?,?)");
          $stmt2->execute([$id, $user_matricule ]);
     }else{
          $stmt2 = $pdo->prepare("DELETE FROM occupy WHERE class_id = ? AND matricule = ?");
          $stmt2->execute([$id, $user_matricule]);
     }

     $stmt = $pdo->prepare("SELECT * FROM classes WHERE Id = ?");
     $stmt->execute([$id]);

     if ($stmt->rowCount() == 1) {
          $data = $stmt->fetch(PDO::FETCH_OBJ);
          $state = $data->state == 1 ? 0 : 1; // Assuming the STATE column is a boolean data type

          $updateStmt = $pdo->prepare("UPDATE classes SET `state` = ? WHERE Id = ?");
          $updateStmt->execute([$state, $id]);

          $response = array("status"=>"success", "message"=>"successfull state change");
          echo json_encode($response);

          exit(); // Add an exit() call after the header redirect to ensure no further code execution
     } else {
          echo "No record found";
     }
} catch (PDOException $e) {
     // Handle and log the exception appropriately
     errorLog($e);
}
