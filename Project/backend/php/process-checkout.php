<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include_once 'db-accessor.php';


$data = json_decode(file_get_contents('php://input'), true);


if (empty($data['items']) || empty($data['address']) || empty($data['city']) || 
    empty($data['deliveryDate']) || empty($data['warehouse']) || 
    empty($data['paymentMethod'])) {
    
    echo json_encode([
        'success' => false,
        'error' => 'Missing required data'
    ]);
    exit();
}


if ($data['paymentMethod'] === 'gift' && empty($data['paymentDetails']['giftCardNumber'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing gift card number'
    ]);
    exit();
} else if (($data['paymentMethod'] === 'credit' || $data['paymentMethod'] === 'debit') && 
          (empty($data['paymentDetails']['cardNumber']) || 
           empty($data['paymentDetails']['cardExpiry']) || 
           empty($data['paymentDetails']['cardCvv']))) {
    
    echo json_encode([
        'success' => false,
        'error' => 'Missing card details'
    ]);
    exit();
}

try {
    $db = new DB();
    $conn = $db->getConnection();
    

    $conn->begin_transaction();
    
    $user_id = 1; // Replace with actual Userid lter
    

    $subtotal = $data['subtotal'];
    $stmt = $conn->prepare("INSERT INTO Shopping (store_code, total_price) VALUES ('ELEC', ?)");
    $stmt->bind_param("d", $subtotal);
    $stmt->execute();
    $receipt_id = $conn->insert_id;
    $stmt->close();
    

    $result = $conn->query("SELECT truck_id FROM Truck WHERE availability_code = 'AVAIL' LIMIT 1");
    
    if ($result->num_rows > 0) {
        $truck = $result->fetch_assoc();
        $truck_id = $truck['truck_id'];
    } else {

        $truck_code = 'TRK' . rand(10, 99);
        $availability = 'AVAIL';
        
        $stmt = $conn->prepare("INSERT INTO Truck (truck_code, availability_code) VALUES (?, ?)");
        $stmt->bind_param("ss", $truck_code, $availability);
        $stmt->execute();
        $truck_id = $conn->insert_id;
        $stmt->close();
    }
    

    $warehouse_id = $data['warehouse'];
    $warehouse_locations = [
        '1' => ['name' => 'Downtown Warehouse', 'code' => 'DTW'],
        '2' => ['name' => 'South Branch', 'code' => 'SBR'],
        '3' => ['name' => 'North Branch', 'code' => 'NBR']
    ];
    
    $source_code = $warehouse_locations[$warehouse_id]['code'];
    $destination_code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['city']), 0, 3));
    

    $shipping = $data['deliveryType'] === 'express' ? 25.00 : 15.00;
    

    $distance = rand(5, 30);
    

    $stmt = $conn->prepare("INSERT INTO Trip (source_code, destination_code, distance, truck_id, price) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdid", $source_code, $destination_code, $distance, $truck_id, $shipping);
    $stmt->execute();
    $trip_id = $conn->insert_id;
    $stmt->close();
    

    $new_availability = 'BUSY';
    $stmt = $conn->prepare("UPDATE Truck SET availability_code = ? WHERE truck_id = ?");
    $stmt->bind_param("si", $new_availability, $truck_id);
    $stmt->execute();
    $stmt->close();
    

    $payment_codes = [
        'credit' => 'CC',
        'debit' => 'DC',
        'gift' => 'GC'
    ];
    $payment_code = $payment_codes[$data['paymentMethod']] . rand(10, 99);
    

    $delivery_date = date('Y-m-d H:i:s', strtotime($data['deliveryDate']));
    

    $total_price = $subtotal + $shipping;
    

    $date_issued = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("INSERT INTO Orders (date_issued, date_received, total_price, payment_code, user_id, trip_id, receipt_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsiii", $date_issued, $delivery_date, $total_price, $payment_code, $user_id, $trip_id, $receipt_id);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();
    

    $conn->commit();
    

    $warehouse_info = $warehouse_locations[$warehouse_id];
    $warehouse_addresses = [
        '1' => '1507 Yonge St, Toronto, ON M4T 1Z2',
        '2' => '25 Queens Quay E, Toronto, ON M5E 1A4',
        '3' => '1000 Murray Ross Pkwy, North York, ON M3J 2P3'
    ];
    
    $time_slots = [
        'morning' => '9:00 AM - 12:00 PM',
        'afternoon' => '1:00 PM - 5:00 PM',
        'evening' => '6:00 PM - 9:00 PM'
    ];
    

    $result = $conn->query("SELECT truck_code FROM Truck WHERE truck_id = $truck_id");
    $truck = $result->fetch_assoc();
    

    $result = $conn->query("SELECT name, address FROM User WHERE user_id = $user_id");
    $user = $result->fetch_assoc();
    
    $orderInfo = [
        'order_id' => $order_id,
        'order_date' => $date_issued,
        'delivery_date' => $delivery_date,
        'total_price' => $total_price,
        'customer_name' => $user['name'],
        'customer_address' => $data['address'] . ', ' . $data['city'],
        'warehouse' => $warehouse_info['name'],
        'warehouse_address' => $warehouse_addresses[$warehouse_id],
        'truck_id' => $truck['truck_code'],
        'items' => $data['items'],
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'deliveryType' => $data['deliveryType'],
        'time_slot' => $time_slots[$data['timeSlot']]
    ];
    

    echo json_encode([
        'success' => true,
        'orderId' => $order_id,
        'orderInfo' => $orderInfo
    ]);
    
} catch (Exception $e) {

    if (isset($conn)) {
        $conn->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'error' => 'Error processing order: ' . $e->getMessage()
    ]);
}
