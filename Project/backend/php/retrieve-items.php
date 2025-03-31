<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
include_once 'db-accessor.php'; include_once 'item.php';

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['department_select'])) {
    $db = new DB();

    $items = $db->get_items($data['department_select']);

    $result = [];

    if (count($items) == 0) {
        echo json_encode($result);
    } else {
        foreach ($items as $item) {
            $result[] = (["id" => $item->get_ID(), "image" => $item->get_image(), "name" => $item->get_name(), "price" => $item->get_price(), "made_in" => $item->get_made_in()]);
        }

        echo json_encode($result);
    }
}
?>