<?php
session_start();

// // For testing purposes, assume user is logged in
// $user_id = 1; // Using a dummy user ID for testing

$cart_data = isset($_POST['cart_data']) ? $_POST['cart_data'] : '[]';
print_r(json_decode($cart_data));

// $cart_data = isset($_POST['cart_data']) ? $_POST['cart_data'] : '[]';
// $cart = json_decode($cart_data, true);


// // if (empty($cart) || empty($_POST['address']) || empty($_POST['city']) || 
// //     empty($_POST['delivery_date']) || empty($_POST['warehouse']) || 
// //     empty($_POST['card_number']) || empty($_POST['expiry']) || empty($_POST['cvv'])) {
// //     header("Location: ../checkout.php");
// //     exit();
// // }


// $address = $_POST['address'];
// $city = $_POST['city'];
// $delivery_date = $_POST['delivery_date'];
// $warehouse_id = (int)$_POST['warehouse'];
// $time_slot = $_POST['time_slot'];


// $card_number = $_POST['card_number'];
// $card_expiry = $_POST['expiry'];
// $cvv = $_POST['cvv'];


// $masked_card = 'XXXX-XXXX-XXXX-' . substr($card_number, -4);


// $warehouse_locations = [
//     1 => ['name' => 'Downtown Warehouse', 'address' => '1507 Yonge St, Toronto, ON M4T 1Z2'],
//     2 => ['name' => 'South Branch', 'address' => '25 Queens Quay E, Toronto, ON M5E 1A4'],
//     3 => ['name' => 'North Branch', 'address' => '1000 Murray Ross Pkwy, North York, ON M3J 2P3']
// ];

// $warehouse_name = $warehouse_locations[$warehouse_id]['name'];
// $warehouse_address = $warehouse_locations[$warehouse_id]['address'];


// $source_code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $warehouse_name), 0, 3));
// $destination_code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $city), 0, 3));


// $subtotal = 0;
// $shipping = 15.00;

// foreach ($cart as $item) {
//     $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
//     $subtotal += $item['price'] * $quantity;
// }

// $total = $subtotal + $shipping;


// $delivery_time = '';
// switch ($time_slot) {
//     case 'morning':
//         $delivery_time = '10:00:00';
//         break;
//     case 'afternoon':
//         $delivery_time = '14:00:00';
//         break;
//     case 'evening':
//         $delivery_time = '18:00:00';
//         break;
//     default:
//         $delivery_time = '12:00:00';
// }

// $expected_delivery = date('Y-m-d H:i:s', strtotime("$delivery_date $delivery_time"));


// $payment_code = 'CC' . rand(10, 99);


// $conn = new mysqli('localhost', 'root', '', 'project');
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


// $conn->begin_transaction();

// try {
//     // Check if user exists, create dummy user if not
//     $check_user = "SELECT user_id FROM User WHERE user_id = ?";
//     $stmt = $conn->prepare($check_user);
//     if (!$stmt) {
//         throw new Exception("User check prepare error: " . $conn->error);
//     }
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $stmt->close();
    
//     if ($result->num_rows == 0) {

//         $sql_user = "INSERT INTO User (user_id, name, tel_no, email, address, city_code, login_id, password, balance) 
//                      VALUES (?, 'Test User', '123-456-7890', 'test@example.com', ?, ?, 'testuser', 'password123', 0)";
//         $stmt = $conn->prepare($sql_user);
//         if (!$stmt) {
//             throw new Exception("User insert prepare error: " . $conn->error);
//         }
//         $city_code = substr($city, 0, 3);
//         $stmt->bind_param("iss", $user_id, $address, $city_code);
//         $stmt->execute();
//         $stmt->close();
//     }
    

//     $sql_shopping = "INSERT INTO Shopping (store_code, total_price) VALUES ('ELEC', ?)";
//     $stmt = $conn->prepare($sql_shopping);
//     if (!$stmt) {
//         throw new Exception("Shopping prepare error: " . $conn->error);
//     }
//     $stmt->bind_param("d", $subtotal);
//     if (!$stmt->execute()) {
//         throw new Exception("Shopping execute error: " . $stmt->error);
//     }
//     $receipt_id = $conn->insert_id;
//     $stmt->close();
    

//     $truck_code = 'TRK' . rand(10, 99);
//     $availability = 'AVAIL';
    

//     $sql_check_truck = "SELECT truck_id FROM Truck WHERE availability_code = 'AVAIL' LIMIT 1";
//     $result = $conn->query($sql_check_truck);
    
//     if ($result && $result->num_rows > 0) {
//         $truck = $result->fetch_assoc();
//         $truck_id = $truck['truck_id'];
//     } else {
//         $sql_new_truck = "INSERT INTO Truck (truck_code, availability_code) VALUES (?, ?)";
//         $stmt = $conn->prepare($sql_new_truck);
//         if (!$stmt) {
//             throw new Exception("Truck prepare error: " . $conn->error);
//         }
//         $stmt->bind_param("ss", $truck_code, $availability);
//         if (!$stmt->execute()) {
//             throw new Exception("Truck execute error: " . $stmt->error);
//         }
//         $truck_id = $conn->insert_id;
//         $stmt->close();
//     }
    
//     // 3. Calculate a simple distance (for example purposes)
//     $distance = rand(5, 30); // Random distance between 5 and 30 km
    
   
//     $sql_trip = "INSERT INTO Trip (source_code, destination_code, distance, truck_id, price) 
//                 VALUES (?, ?, ?, ?, ?)";
//     $stmt = $conn->prepare($sql_trip);
//     if (!$stmt) {
//         throw new Exception("Trip prepare error: " . $conn->error);
//     }
//     $stmt->bind_param("ssdid", $source_code, $destination_code, $distance, $truck_id, $shipping);
//     if (!$stmt->execute()) {
//         throw new Exception("Trip execute error: " . $stmt->error);
//     }
//     $trip_id = $conn->insert_id;
//     $stmt->close();
    

//     $new_availability = 'BUSY';
//     $sql_update_truck = "UPDATE Truck SET availability_code = ? WHERE truck_id = ?";
//     $stmt = $conn->prepare($sql_update_truck);
//     if (!$stmt) {
//         throw new Exception("Update truck prepare error: " . $conn->error);
//     }
//     $stmt->bind_param("si", $new_availability, $truck_id);
//     if (!$stmt->execute()) {
//         throw new Exception("Update truck execute error: " . $stmt->error);
//     }
//     $stmt->close();
    

//     $sql_order = "INSERT INTO Orders (date_issued, date_received, total_price, payment_code, user_id, trip_id, receipt_id) 
//                 VALUES (NOW(), ?, ?, ?, ?, ?, ?)";
//     $stmt = $conn->prepare($sql_order);
//     if (!$stmt) {
//         throw new Exception("Order prepare error: " . $conn->error);
//     }
//     $stmt->bind_param("sdsiii", $expected_delivery, $total, $payment_code, $user_id, $trip_id, $receipt_id);
//     if (!$stmt->execute()) {
//         throw new Exception("Order execute error: " . $stmt->error);
//     }
//     $order_id = $conn->insert_id;
//     $stmt->close();
    

    

//     $conn->commit();
    
//     // Store order info in session for order confirmation page
//     $_SESSION['order_info'] = [
//         'order_id' => $order_id,
//         'order_date' => date('Y-m-d H:i:s'),
//         'delivery_date' => $expected_delivery,
//         'total_price' => $total,
//         'customer_name' => 'Test User', // Dummy name
//         'customer_address' => $address . ', ' . $city,
//         'warehouse' => $warehouse_name,
//         'warehouse_address' => $warehouse_address,
//         'truck_id' => $truck_code,
//         'items' => $cart,
//         'subtotal' => $subtotal,
//         'shipping' => $shipping
//     ];
    

//     unset($_SESSION['cart']);
    

//     header("Location: ../order-confirmation.php?order_id=$order_id");
//     exit();
    
// } catch (Exception $e) {

//     $conn->rollback();
//     echo "Error processing order: " . $e->getMessage();

//     exit();
// }


// $conn->close();
?>
