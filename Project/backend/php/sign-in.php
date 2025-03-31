<?php 
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
include 'db-accessor.php';

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['username']) && isset($data['password'])) {
    try {
        $db = new DB();

        $res = $db->check_user($data['username'], $data['password']);

        if ($res) {
            setcookie("logged-name", $res['name'], time()+10800, '/');
            setcookie("logged-type", $res['type'], time()+10800, '/');
            echo json_encode(["user_exists" => true, "error_code" => 0]);
        } else {
            echo json_encode(["user_exists" => false, "error_code" => 0, "error" => "Invalid credentials."]);
        }
    } catch (Exception $e) {
        echo json_encode(["user_exists" => false, "error_code" => 2, "error" => "Database error."]);
    }
} else {
    echo json_encode(["user_exists" => false, "error_code" => 3, "error" => "Missing username or password."]);
}
?>