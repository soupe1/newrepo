<?php
    header("Access-Control-Allow-Origin: http://localhost:4200");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Credentials: true");
    include_once 'db-accessor.php';

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project";

    $db = new DB();

    $conn = new mysqli($servername, $username, $password, $dbname);
        
    $err = false;

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    try {
        $conn->query("CREATE TABLE User (
        user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        tel_no VARCHAR(20) NOT NULL,
        email VARCHAR(320) NOT NULL UNIQUE,
        address VARCHAR(255) NOT NULL,
        city_code VARCHAR(3) NOT NULL,
        login_id VARCHAR(32) NOT NULL UNIQUE,
        password VARCHAR(60) NOT NULL,
        salt VARCHAR(16) NOT NULL,
        balance DECIMAL(10, 2) DEFAULT 0,
        type VARCHAR(10) NOT NULL);"); // 'user' for normal, 'admin' for admin
    } catch (Exception $e) {
        $err = true;
    }

    try {
        $conn->query("CREATE TABLE Truck (
        truck_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        truck_code VARCHAR(5),
        availability_code VARCHAR(5));");
    } catch (Exception $e) {
        $err = true;
    }

    try {
        $conn->query("CREATE TABLE Trip (
        trip_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        source_code VARCHAR(3) NOT NULL,
        destination_code VARCHAR(3) NOT NULL,
        distance DECIMAL(12, 4),
        truck_id INT UNSIGNED NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (truck_id) REFERENCES Truck(truck_id));");
    } catch (Exception $e) {
        $err = true;
    }

    try {
        $conn->query("CREATE TABLE Shopping (
        receipt_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        store_code VARCHAR(5),
        total_price DECIMAL(10, 2) NOT NULL);");
    } catch (Exception $e) {
        $err = true;
    }

    try {
        $conn->query("CREATE TABLE Orders (
        order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        date_issued DATETIME NOT NULL,
        date_received DATETIME,
        total_price DECIMAL(10, 2) NOT NULL,
        payment_code VARCHAR(5),
        user_id INT UNSIGNED NOT NULL,
        trip_id INT UNSIGNED NOT NULL,
        receipt_id INT UNSIGNED NOT NULL,
        FOREIGN KEY (user_id) REFERENCES User(user_id),
        FOREIGN KEY (trip_id) REFERENCES Trip(trip_id),
        FOREIGN KEY (receipt_id) REFERENCES Shopping(receipt_id));");
    } catch (Exception $e) {
        $err = true;
    }

    try {
        $conn->query("CREATE TABLE Item (
        item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        item_name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        made_in VARCHAR(255) NOT NULL,
        image BLOB NOT NULL,
        department_code VARCHAR(255) NOT NULL);");

        $img = file_get_contents("../images/nintendo-switch.jpg");
        $db->insert_item("Nintendo Switch", 399.99, "Japan", "$img", "electronics");

        $img = file_get_contents("../images/nintendo-switch-oled.jpg");
        $db->insert_item("Nintendo Switch OLED", 449.99, "Japan", "$img", "electronics");

        $img = file_get_contents("../images/nintendo-switch-lite.jpg");
        $db->insert_item("Nintendo Switch LITE", 259.99, "Japan", "$img", "electronics");

        $img = file_get_contents("../images/nintendo-switch-pro-con.jpg");
        $db->insert_item("Nintendo Switch Pro Controller", 89.99, "Japan", "$img", "electronics");

        $img = file_get_contents("../images/nintendo-switch-joy-con.jpg");
        $db->insert_item("Nintendo Switch Joy-Con", 99.99, "Japan", "$img", "electronics");
    } catch (Exception $e) {
        $err = true;
    }

    if ($err) {
        echo json_encode(["error_code" => 1]);
    } else {
        echo json_encode(["error_code" => 0]);
    }

    $conn->close();
?> 