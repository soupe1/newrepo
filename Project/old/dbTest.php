<?php
include 'php/db-accessor.php';
$db = new DB();

// $img = file_get_contents("images/nintendo-switch.jpg");

// echo $db->insert_item("Nintendo Switch", 449.99, "Japan", "$img", "electronics");

// $item = $db->get_items()[1];
// echo 'data:image/jpeg;base64,"' . base64_encode($db->get_item(1)["image"]) . '"';
echo "<img src='data:image/jpeg;base64," . base64_encode($db->get_item(1)["image"]) . "'>";
echo "<img src=".$item->get_image().">";
?>