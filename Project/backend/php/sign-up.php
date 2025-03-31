<?php 
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
include 'db-accessor.php';

function generateRandomSalt() {
    return base64_encode(random_bytes(12));
}

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['firstName']) && isset($data['lastName']) && isset($data['phone']) && isset($data['email']) && isset($data['address']) && isset($data['city']) && isset($data['province']) && isset($data['country']) && isset($data['postalCode']) && isset($data['username']) && isset($data['password']) && isset($data['type'])) {
    try {
        $db = new DB();
        
        $name = $data['firstName']." ".$data['lastName'];
        $address = $data['address'].", ".$data['city'].", ".$data['province'].", ".$data['country'].", ".$data['postalCode'];
        $city_code = strtoupper(substr($data['city'], 0, 3));

        $salt = generateRandomSalt();
        $password_salt = $data['password'] . $salt;

        $res = $db->insert_user($name, $data['phone'], $data['email'], $address, $city_code, $data['username'], md5($password_salt), $salt, $data['type']);

        if ($res) {
            setcookie("logged-name", $name, time()+10800, '/');
            setcookie("logged-type", $data['type'], time()+10800, '/');
            echo json_encode(["error_code" => 0]);
        } else {
            echo json_encode(["error_code" => 2, "error" => "Error creating user."]);
        }
    } catch (Exception $e) {
        echo json_encode(["error_code" => 3, "error" => "Database error."]);
    }
} else {
    echo json_encode(["error_code" => 4, "error" => "Missing input data."]);
}
?>