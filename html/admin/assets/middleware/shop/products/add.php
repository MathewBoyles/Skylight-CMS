<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["product"] = [
  "title" => $_POST["value"]["title"] ?? "",
  "description" => $_POST["value"]["description"] ?? "",
  "image" => $_POST["value"]["image"] ?? "",
  "price" => $_POST["value"]["price"] ?? 0,
  "compare_price" => $_POST["value"]["compare_price"] ?? 0,
  "quantity" => $_POST["value"]["quantity"] ?? ""
];

$data["product"]["price"] = is_numeric($data["product"]["price"]) ? ($data["product"]["price"] * 100) : 0;
$data["product"]["compare_price"] = is_numeric($data["product"]["compare_price"]) ? ($data["product"]["compare_price"] * 100) : 0;

$hasError = false;

if ($csrfOkay && isset($_POST["value"])) {
    if (is_array($_POST["value"])) {
        if (!($_POST["value"]["title"] ?? false)) {
            $hasError = true;

            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Product title required"
            ]);
        }

        if (!in_array(($_POST["value"]["image"] ?? ""), $data["images"])) {
            $hasError = true;
            $data["product"]["image"] = "";

            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Product image required"
            ]);
        }

        $_POST["value"]["price"] = $_POST["value"]["price"] ?? "";
        if ($_POST["value"]["price"] < 0 || !is_numeric($_POST["value"]["price"])) {
            $hasError = true;
            $data["product"]["price"] = "0";

            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Product price cannot be less than $0.00"
            ]);
        }

        $_POST["value"]["compare_price"] = $_POST["value"]["compare_price"] ?? "";
        if ($_POST["value"]["compare_price"] && ($_POST["value"]["compare_price"] < $_POST["value"]["price"] || !is_numeric($_POST["value"]["compare_price"]))) {
            $hasError = true;
            $data["product"]["compare_price"] = "";

            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Product compare price cannot be less than the display price"
            ]);
        }

        if (($_POST["value"]["quantity"] ?? -1) < 0 || $_POST["value"]["quantity"] == "") {
            $hasError = true;
            $data["product"]["quantity"] = "";

            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Product inventory cannot be less than zero"
            ]);
        }

        if (!$hasError) {
            $db->insert("shop_items", [
              "title" => $_POST["value"]["title"],
              "description" => $_POST["value"]["description"],
              "price" => $_POST["value"]["price"] * 100,
              "compare_price" => $_POST["value"]["compare_price"] * 100,
              "image" => $_POST["value"]["image"],
              "quantity" => $_POST["value"]["quantity"]
            ]);

            header("Location: /admin/shop/products?msg=created");
            exit;
        }
    }
}
