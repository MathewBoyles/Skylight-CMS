<?php
if (!$pageArray["editor"]) {
    if (!($_GET["key"] ?? false)) {
        header("Location: /shop/cart");
        exit;
    }

    $data["cart"] = Cart::list($_GET["key"]);

    if (!$data["cart"]["items"]) {
        header("Location: /shop/cart");
        exit;
    }
}
