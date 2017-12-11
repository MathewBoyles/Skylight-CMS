<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["product"] = false;

if (isset($_GET["id"])) {
    $data["product"] = Shop::item($_GET["id"]);

    if (isset($_POST["delete"]) && $csrfOkay) {
        $db->update("shop_items", ["deleted" => "1"], ["id" => $_GET["id"]]);

        header("Location: /admin/shop/products?msg=deleted");
        exit;
    }
}
