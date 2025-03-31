<?php
class Item {
    private $item_id;
    private $item_name;
    private $price;
    private $made_in;
    private $image;
    private $department_code;

    // private static $items = [];

    function __construct($item_id, $item_name, $price, $made_in, $image, $department_code) {
        $this->item_id = $item_id;
        $this->item_name = $item_name;
        $this->price = $price;
        $this->made_in = $made_in;
        $this->image = $image;
        $this->department_code = $department_code;
    }

    function get_id() {
        return $this->item_id;
    }

    function get_name() {
        return $this->item_name;
    }

    function set_name($item_name) {
        $this->item_name = $item_name;
    }

    function get_price() {
        return $this->price;
    }

    function set_price($price) {
        $this->price = $price;
    }

    function get_made_in() {
        return $this->made_in;
    }

    function set_made_in($made_in) {
        $this->made_in = $made_in;
    }

    function get_image() {
        return $this->image;
    }

    function set_image($image) {
        $this->image = $image;
    }

    function get_dept_code() {
        return $this->department_code;
    }

    function set_dept_code($department_code) {
        $this->department_code = $department_code;
    }

    // static function add_item($item) {
    //     self::$items[] = $item;
    // }

    // static function get_items() {
    //     return self::$items;
    // }

    // static function reset_items() {
    //     self::$items = [];
    // }
}
?>