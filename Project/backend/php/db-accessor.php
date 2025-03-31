<?php
include_once "item.php";
class DB {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    function __construct($servername="localhost", $username="root", $password="", $dbname="project") {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->Connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    }

    public function getTableNames() {
        try {
            $tables = [];
            $result = $this->conn->query("SHOW TABLES;");
            while ($row = $result->fetch_assoc()) {
                $tables[] = $row["Tables_in_project"];
            }
            return($tables);
        } catch (Exception $e) {
            return null;
        }
    }

    function insert($table, $item_name, $price, $made_in, $image, $department_code) {
        switch ($table) {
            case 'item':
                $this->insert_item($item_name, $price, $made_in, $image, $department_code);
                break;
            default:
                break;
        }
    }

    function insert_item($item_name, $price, $made_in, $image, $department_code) {
        try {
            $sql = $this->conn->prepare("INSERT INTO item (item_name, price, made_in, image, department_code) VALUES ('$item_name', '$price', '$made_in', ?, '$department_code')");
            $sql->bind_param('s', $image);
            $sql->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function get_item($item_id) {
        try {
            $result = $this->conn->query("SELECT * FROM item WHERE item_id = $item_id;");
            return $result->fetch_assoc();
        } catch (Exception $e) {
            return false;
        }
    }

    function get_items($department_code) {
        try {
            $item_array = [];
            $result = $this->conn->query("SELECT * FROM item WHERE department_code = '$department_code';");
            // $result->fetch_assoc();
            foreach ($result as $item) {
                $item_array[] = new Item($item['item_id'], $item['item_name'], $item['price'], $item['made_in'], 'data:image/jpeg;base64,'.base64_encode($item["image"]), $item["department_code"]);
            }

            return $item_array;
        } catch (Exception $e) {
            return $e->getMessage()."SELECT * FROM item WHERE department_code = $department_code;";
        }
    }

    function insert_user($name, $tel_no, $email, $address, $city_code, $login_id, $hashed_password, $salt, $type) {
        try {
            $sql = "INSERT INTO user (name, tel_no, email, address, city_code, login_id, password, salt, type) VALUES ('$name', '$tel_no', '$email', '$address', '$city_code', '$login_id', '$hashed_password', '$salt', '$type')";
            $this->conn->query($sql);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function check_user($login_id, $check_password) {
        try {
            $sql = "SELECT name, login_id, password, salt, type FROM user WHERE login_id = '$login_id'";
            $result = $this->conn->query($sql);

            if ($result->num_rows) {
                $user = $result->fetch_assoc();
                $stored_password = $user['password'];
                $stored_salt = $user['salt'];

                $check_password = md5($check_password . $stored_salt);
                    
                if ($check_password == $stored_password) {
                    return ["name" => $user['name'], "type" => $user['type']];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
?>