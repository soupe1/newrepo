<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include_once 'db-accessor.php';


$data = json_decode(file_get_contents('php://input'), true);


if (empty($data['order_id']) || empty($data['item_id']) || 
    !isset($data['rating']) || empty($data['review'])) {
    
    echo json_encode([
        'success' => false,
        'error' => 'Missing required data'
    ]);
    exit();
}


$rating = intval($data['rating']);
if ($rating < 1 || $rating > 5) {
    echo json_encode([
        'success' => false,
        'error' => 'Rating must be between 1 and 5'
    ]);
    exit();
}


if (strlen($data['review']) > 100) {
    echo json_encode([
        'success' => false,
        'error' => 'Review cannot exceed 100 characters'
    ]);
    exit();
}

try {
    $db = new DB();
    $conn = $db->getConnection();
    

    $conn->query("CREATE TABLE IF NOT EXISTS Review (
        review_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL,
        item_id INT UNSIGNED NOT NULL,
        rating INT NOT NULL,
        review VARCHAR(100) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES Orders(order_id),
        FOREIGN KEY (item_id) REFERENCES Item(item_id)
    )");
    

    $stmt = $conn->prepare("INSERT INTO Review (order_id, item_id, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $data['order_id'], $data['item_id'], $rating, $data['review']);
    $stmt->execute();
    $review_id = $conn->insert_id;
    $stmt->close();
    

    echo json_encode([
        'success' => true,
        'review_id' => $review_id
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error submitting review: ' . $e->getMessage()
    ]);
}