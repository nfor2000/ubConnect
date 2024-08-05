<?php
session_start();

require_once('./components/errorlog.php');
require_once('./db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $matricule = $_POST['matricule'];
    $password = $_POST['password'];

    if (empty($matricule) || empty($password)) {
        $response = array('status' => 'error', 'message' => 'All fields are required');
        echo json_encode($response);
    } else {
        try {
            $sql = "SELECT * FROM users WHERE matricule = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$matricule]);

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_OBJ);
                $stored_password = $user->password;
                $is_correct_password = password_verify($password, $stored_password);

                if ($is_correct_password) {
                    $_SESSION["matricule"] = $matricule;
                    $response = array('status' => 'success', 'message' => 'Login successful');
                    echo json_encode($response);
                } else {
                    $response = array('status' => 'error', 'message' => 'Wrong password');
                    echo json_encode($response);
                }
            } else {
                $response = array('status' => 'error', 'message' => 'User does not exist');
                echo json_encode($response);
            }
        } catch (PDOException $e) {
            errorLog($e);
            $response = array('status' => 'error', 'message' => 'An error occurred');
            echo json_encode($response);
        }
    }
}
?>