<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include_once 'db-accessor.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid order ID'
    ]);
    exit();
}

try {
    $db = new DB();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT o.order_id, o.date_issued, o.date_received, o.total_price, o.payment_code,
               u.name AS customer_name, u.address AS customer_address,
               tr.truck_code, t.source_code, t.destination_code, t.price AS shipping_price
        FROM Orders o
        JOIN User u ON o.user_id = u.user_id
        JOIN Trip t ON o.trip_id = t.trip_id
        JOIN Truck tr ON t.truck_id = tr.truck_id
        WHERE o.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Order not found'
        ]);
        exit();
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();
    
    $warehouse_codes = [
        'DTW' => ['id' => '1', 'name' => 'Downtown Warehouse', 'address' => '1507 Yonge St, Toronto, ON M4T 1Z2'],
        'SBR' => ['id' => '2', 'name' => 'South Branch', 'address' => '25 Queens Quay E, Toronto, ON M5E 1A4'],
        'NBR' => ['id' => '3', 'name' => 'North Branch', 'address' => '1000 Murray Ross Pkwy, North York, ON M3J 2P3']
    ];
    
    $warehouse = $warehouse_codes[$order['source_code']] ?? [
        'id' => '0', 
        'name' => 'Unknown Warehouse', 
        'address' => 'Unknown Address'
    ];
    
    //gotta change it to get items from table for now getting items from shopping and item tables
    
    $stmt = $conn->prepare("SELECT receipt_id FROM Orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $receipt_result = $stmt->get_result();
    $receipt = $receipt_result->fetch_assoc();
    $receipt_id = $receipt['receipt_id'];
    $stmt->close();
    
    $items_result = $conn->query("SELECT item_id, item_name, price FROM Item LIMIT 5");
    $items = [];
    
    while ($item = $items_result->fetch_assoc()) {
        $quantity = rand(1, 3);
        
        $review_stmt = $conn->prepare("
            SELECT review_id FROM Review 
            WHERE order_id = ? AND item_id = ?
        ");
        $review_stmt->bind_param("ii", $order_id, $item['item_id']);
        $review_stmt->execute();
        $review_result = $review_stmt->get_result();
        $reviewed = $review_result->num_rows > 0;
        $review_stmt->close();
        
        $items[] = [
            'item_id' => $item['item_id'],
            'item_name' => $item['item_name'],
            'price' => floatval($item['price']),
            'quantity' => $quantity,
            'reviewed' => $reviewed
        ];
    }
    

    $subtotal = array_reduce($items, function($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);
    

    $deliveryType = $order['shipping_price'] > 15 ? 'express' : 'regular';
    

    $orderInfo = [
        'order_id' => $order['order_id'],
        'order_date' => $order['date_issued'],
        'delivery_date' => $order['date_received'],
        'total_price' => floatval($order['total_price']),
        'customer_name' => $order['customer_name'],
        'customer_address' => $order['customer_address'],
        'warehouse' => $warehouse['name'],
        'warehouse_address' => $warehouse['address'],
        'truck_id' => $order['truck_code'],
        'items' => $items,
        'subtotal' => $subtotal,
        'shipping' => floatval($order['shipping_price']),
        'deliveryType' => $deliveryType
    ];
    

    echo json_encode([
        'success' => true,
        'orderInfo' => $orderInfo
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error retrieving order: ' . $e->getMessage()
    ]);
}