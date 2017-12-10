<?php
if (isset($_POST["add"])) {
    if (is_numeric($_POST["add"])) {
        Cart::add($_POST["add"]);
    }
}

if (isset($_POST["quantity"])) {
    if (is_array($_POST["quantity"])) {
        foreach ($_POST["quantity"] as $itemID => $quantity) {
            Cart::set($itemID, $quantity);
        }
    }
}

if (!$pageArray["editor"]) {
    $pageArray["cart"] = Cart::list();
}
