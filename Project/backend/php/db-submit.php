<?php

include_once '../php/db-accessor.php'; include_once '../php/item.php';

$db = new DB();

if (isset($_POST['table-select']) and isset($_POST['item_name']) and isset($_POST['price']) and isset($_POST['made_in']) and !empty($_FILES) and isset($_POST['department_code'])) {
    $image = file_get_contents($_FILES['image']['tmp_name']);
    
    $table_operation = explode("/", $_POST['table-select']);
    
    $table = $table_operation[0];
    $operation = $table_operation[1];

    switch ($operation) {
        case 'insert':
            $db->insert($table, $_POST['item_name'], $_POST['price'], $_POST['made_in'], $image, $_POST['department_code']);
            break;
        default:
            break;
    }

} elseif (isset($_POST['department_select'])) {
    $items = $db->get_items($_POST['department_select']);

    if (count($items) == 0) {
        echo "<div class='col-lg-4 col-md-6 offset-lg-4 offset-md-3'>No Items Available</div>";
    } else {
        $result = "<div class='row' id='item-list-display'>";
        foreach ($items as $item) {
            $result .= "<div draggable='true' class='col-lg-4 col-md-6 item' data-value=".$item->get_ID()."><img draggable='false' src=".$item->get_image().">";
            $result .= "<div class='item-name'>".$item->get_name()."</div>";
            $result .= "<div class='item-price'>$".$item->get_price()."</div>";
            $result .= "<div class='item-made-in'>Made In: ".$item->get_made_in()."</div>";
            $result .= "</div>";
        }
        $result .= "</div>";
        $result .= "<div class='add-parent fixed-bottom'><div class='shopping-cart-add' id='cart-drop-zone'>Drag Items Here to Add to Cart!</div></div>";
        echo $result;
    }

} elseif (isset($_POST['cart_items'])) {
    $cart = $_POST['cart_items'];
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
}

function in_json($check_item, $json_array) {
    foreach($json_array as $key => $item) {
        if ($item['item_id'] == $check_item['item_id']) {
            return $key;
        }
    }
    return false;
}
?>