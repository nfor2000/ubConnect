<?php
require_once('./db_connect.php');
require_once('./components/errorlog.php');

$fetch_course_stmt = $pdo->query("SELECT * FROM course_table");
$courses = $fetch_course_stmt->fetchAll(PDO::FETCH_OBJ);

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
     $name = $_POST['name'];
     $email = $_POST['email'];
     $matricule = $_POST['matricule'];
     $password = $_POST['password'];
     $course_id = intval($_POST['course']);

     if (empty($name) || empty($email) || empty($matricule) || empty($password)) {
          $response = array("status"=>"error", "message"=>"all fields required");
          echo json_encode($response);
     } elseif (!empty($name) && !empty($email) && !empty($matricule) && !empty($password) && empty($course_id)) {
          $hash_password = password_hash($password, PASSWORD_BCRYPT);

          try {
               //code...
               $sql = "SELECT * FROM users WHERE matricule = ?";
               $stmt = $pdo->prepare($sql);
               $stmt->execute([$matricule]);

               if ($stmt->rowCount() > 0) {
                    $response = array("status"=>"error", "message"=>"User already exist");
                    echo json_encode($response);
               } else {

                    $sql = "INSERT INTO users (`name`,email,matricule,`password`) VALUES (?,?,?,?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$name, $email, strtoupper($matricule), $hash_password]);
                    $response = array("status"=>"success", "message"=>"User created successfully");
                    echo json_encode($response);
               }
          }
          catch(PDOException $e) {
               errorLog($e);
          } 
     }
              
      elseif (!empty($name) && !empty($email) && !empty($matricule) && !empty($password) && !empty($course_id)) {
          $hash_password = password_hash($password, PASSWORD_BCRYPT);
                    $stmt_delegates = $pdo->prepare("SELECT * FROM delegates WHERE course_id = ?");
                    $stmt_delegates->execute([$course_id]);

                    if ($stmt_delegates->rowCount() === 0) {
                        

                         try {
                              //code...
                              $sql = "SELECT * FROM users WHERE matricule = ?";
                              $stmt = $pdo->prepare($sql);
                              $stmt->execute([$matricule]);
               
                              if ($stmt->rowCount() > 0) {
                                   $response = array("status"=>"error", "message"=>"User already exist");
                                   echo json_encode($response);
                              } else {

                                   $sql = "INSERT INTO users (`name`,email,matricule,`password`) VALUES (?,?,?,?)";
                                   $stmt = $pdo->prepare($sql);
                                   $stmt->execute([$name, $email, strtoupper($matricule), $hash_password]);
                                   $response = array("status"=>"success", "message"=>"User created successfully");
                                   echo json_encode($response);
                              
                                   $stmt_add_delegate = $pdo->prepare("INSERT INTO delegates (matricule, course_id) VALUES (?, ?)");
                                   $stmt_add_delegate->execute([ strtoupper($matricule), $course_id]);

                              }

                           
                    }
                    catch(PDOException $e) {
                         errorLog($e);
                    }  

                        
                    } else {
                         $response = array(["status"=>"error", "message"=>"Delegate already exist for this course. Contact database admin"]);
                         echo json_encode($response);
               }

          }
            
     }
?>

