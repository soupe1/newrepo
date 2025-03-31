<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
include_once 'db-accessor.php'; include_once 'item.php';

function in_json($check_item, $json_array) {
    foreach($json_array as $key => $item) {
        if ($item['item_id'] == $check_item['item_id']) {
            return $key;
        }
    }
    return false;
}

$db = new DB();

$cart = json_decode(file_get_contents('php://input'), true);
$result = [];

foreach ($cart as $item) {
    $new_item = $db->get_item($item);
    
    unset($new_item['image']); // was preventing JSON encoding, and is quite a large amount of unneeded data
    $new_item['item_id'] = intval($new_item['item_id']);
    $new_item['price'] = floatval($new_item['price']);

    if (($key = in_json($new_item, $result)) !== false) {
        $result[$key]['quantity'] += 1;
    } else {
        $new_item['quantity'] = 1;
        $result[] = $new_item;
    }
}

echo json_encode($result);
?>