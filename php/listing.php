<?php
session_start();


require_once('./db_connect.php');
require_once('./components/errorlog.php');


$user_matricule = $_SESSION['matricule'];


try {
     //code...
     $if_occupies_class_stmt  = $pdo->prepare("SELECT * FROM occupy WHERE matricule = ?");
     $if_occupies_class_stmt->execute([$user_matricule]);

     $occupies_class = $if_occupies_class_stmt->rowCount() > 0 ? true : false;

     $stmt_verify_delegate = $pdo->prepare("SELECT * FROM delegates WHERE matricule = ?");
     $stmt_verify_delegate->execute([$user_matricule]);

     $is_delegate = $stmt_verify_delegate->rowCount() > 0 ? true : false;
} catch (PDOException $e) {
     errorLog($e);
}

try {
     if (isset($_REQUEST["class"]) && isset($_REQUEST["state"])) {

          $search = $_REQUEST["class"];
          $state = $_REQUEST["state"] == 'occupied' ? 1 : 0;
          $stmt_search = $pdo->prepare("SELECT * FROM classes WHERE class_name LIKE CONCAT(?, '%') AND `state` = ? ");
          $stmt_search->execute([$search, $state]);
          $classes = $stmt_search->fetchAll(PDO::FETCH_OBJ);
     } else if (isset($_REQUEST["class"])) {
          $search = $_REQUEST["class"];
          $stmt_search = $pdo->prepare("SELECT * FROM classes WHERE class_name LIKE CONCAT(?, '%')");
          $stmt_search->execute([$search]);
          $classes = $stmt_search->fetchAll(PDO::FETCH_OBJ);
     } else if (isset($_REQUEST["state"]) && $_REQUEST["state"]  != "all") {

          $state = $_REQUEST["state"] == 'occupied' ? 1 : 0;
          $stmt_filter = $pdo->prepare("SELECT * FROM classes WHERE `state` = ?");
          $stmt_filter->execute([$state]);
          $classes = $stmt_filter->fetchAll(PDO::FETCH_OBJ);
     } else {

          $stmt = $pdo->query("SELECT * FROM classes");
          $classes = $stmt->fetchAll(PDO::FETCH_OBJ);
     }
} catch (PDOException $e) {
     errorLog($e);
}
$result;
foreach ($classes as $class) {
     $id = $class->Id;
     $current_time = date("H:i:s");
     $day = strtoupper(date('l'));
     $cond_a = ($class->state);

     try {
          $stmt_class_occupant = $pdo->prepare("SELECT * FROM occupy WHERE matricule = ? AND class_id = ?");
          $stmt_class_occupant->execute([$user_matricule, $id]);
          $is_class_occupant = $stmt_class_occupant->rowCount() == 1 ? true : false;
          //code...
          $stmt = $pdo->prepare("SELECT * FROM final_table WHERE CLASS_ID = ? AND `Day` = ?");
          $stmt->execute([$id, $day]);
          if ($stmt->rowCount() > 0) {
               $final_classes = $stmt->fetchAll(PDO::FETCH_OBJ);

               foreach ($final_classes as $final_class) {
                    $currentTime = strtotime($current_time) - 60 * 60;
                    $startTime = strtotime($final_class->START_TIME);
                    $stopTime = strtotime($final_class->STOP_TIME);

                    $cond_b = ($currentTime >= $startTime);
                    $cond_c = ($currentTime <= $stopTime);


                    $stmt_is_course_delegate = $pdo->prepare("SELECT * FROM delegates WHERE matricule = ? AND course_id = ?");
                    $stmt_is_course_delegate->execute([$user_matricule, $final_class->COURSE_ID]);

                    $is_course_delegate = $stmt_is_course_delegate->rowCount() == 1 ? true : false;
               }


               if ($cond_b && $cond_c  && $cond_a && !$is_course_delegate) {
                    $state = "expected occupied";
               } else if (($cond_b && $cond_c && $cond_a) ||  $cond_a) {
                    $state = "class occupied";
               } else if ($cond_b && $cond_c  && !$cond_a) {
                    $state = "expected";
               } else {
                    $state = "free";
               }
          } else {
               if ($cond_a) {
                    $state = "class occupied";
               } else {
                    $state = "free";
               }
          }

          $bg_color = (strcasecmp($state, 'free') == 0) ? "bg-success" : (strcasecmp($state, "class occupied") == 0 ? "bg-danger" : (strcasecmp($state, "expected occupied") == 0 ? "bg-purple" : "bg-warning"));
     } catch (PDOException $e) {
          echo "An error occured: " . $e->getMessage();
     }

     $result = $result . "<tr>
                              <td>$class->class_name</td>
                              <td>
                                   <div class='circle $bg_color'></div>
                              </td>
                              <td>";

     if (strcasecmp($state, 'class occupied') == 0 || strcasecmp($state, 'expected occupied') == 0) {
          if ($is_delegate) {
               if ($is_class_occupant) {
                    $result = $result . "<button onclick='toggleClass($class->Id)' class='btn btn-primary'>Release</button>";
               } else {

                    $result = $result . "<button onclick='' class='btn btn-primary disabled'>occupied</button>";
               }
          } else {

               $result = $result .  "<button onclick='' class='btn btn-primary disabled'>occupied</button>";
          }
     } else {
          if ($is_delegate && !$occupies_class) {

               $result = $result . "<button onclick='toggleClass($class->Id)' class='btn btn-danger '>Occupy</button>";
          } else {
               $result = $result . "<button onclick='' class='btn btn-danger disabled'>Occupy</button>";
          }
     }

     $result =  $result . "  </td></tr>";
     // $result = $result . "<tr><td>" . $class->class_name . "</td></tr>";
}

if (isset($_REQUEST['session'])) {
     $result = $user_matricule;
}
echo $result;
