<?php
if (!$pageArray["editor"]) {
    if (!$pageArray["cart"]["items"]) {
        header("Location: /shop/cart");
        exit;
    }
}

if (!$pageArray["editor"] && ($_POST["checkout"] ?? false) == "1" && $pageArray["cart"]["items"] && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["phone"])) {
    $key = Cart::purchase($_POST);

    header("Location: /shop/cart/success?key=" . $key);
    exit;
}
