<?php
$data["alerts"] = array();

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["product"] = Shop::item($_GET["id"] ?? "", true);

if ($data["product"]) {
    $data["product"]["original_quantity"] = $data["product"]["quantity"];

    if ($csrfOkay && isset($_POST["value"])) {
        if (is_array($_POST["value"])) {
            $update = [];

            if ($_POST["value"]["title"] ?? false) {
                if ($_POST["value"]["title"] !== $data["product"]["title"]) {
                    $update["title"] = $_POST["value"]["title"];

                    array_push($data["alerts"], [
                      "class" => "success",
                      "message" => "Product title updated"
                    ]);
                }
            } else {
                array_push($data["alerts"], [
                  "class" => "warning",
                  "message" => "Product title required"
                ]);
            }

            if (($_POST["value"]["description"] ?? false) !== $data["product"]["description"]) {
                $update["description"] = $_POST["value"]["description"];

                array_push($data["alerts"], [
                  "class" => "success",
                  "message" => "Product description updated"
                ]);
            }

            if (in_array($_POST["value"]["image"] ?? false, $data["images"])) {
                if ($_POST["value"]["image"] !== $data["product"]["image"]) {
                    $update["image"] = $_POST["value"]["image"];

                    array_push($data["alerts"], [
                      "class" => "success",
                      "message" => "Product image updated"
                    ]);
                }
            } else {
                array_push($data["alerts"], [
                  "class" => "warning",
                  "message" => "Product image required"
                ]);
            }

            $_POST["value"]["price"] = $_POST["value"]["price"] ?? ($data["product"]["price"] / 100);
            if (is_numeric($_POST["value"]["price"] ?? false)) {
                $_POST["value"]["price"] = round($_POST["value"]["price"], 2);
            }
            if ($_POST["value"]["price"] != ($data["product"]["price"] / 100)) {
                if ($_POST["value"]["price"] > 0 && is_numeric($_POST["value"]["price"])) {
                    $update["price"] = $_POST["value"]["price"] * 100;

                    array_push($data["alerts"], [
                      "class" => "success",
                      "message" => "Product price updated"
                    ]);
                } else {
                    array_push($data["alerts"], [
                      "class" => "warning",
                      "message" => "Product price cannot be less than $0.00"
                    ]);
                }
            }

            $_POST["value"]["compare_price"] = $_POST["value"]["compare_price"] ?? ($data["product"]["compare_price"] / 100);
            if (is_numeric($_POST["value"]["compare_price"] ?? false)) {
                $_POST["value"]["compare_price"] = round($_POST["value"]["compare_price"], 2);
            }
            if ($_POST["value"]["compare_price"] != ($data["product"]["compare_price"] / 100)) {
                if (($_POST["value"]["compare_price"] > 0 && is_numeric($_POST["value"]["compare_price"])) || $_POST["value"]["compare_price"] == "") {
                    if ($_POST["value"]["compare_price"] > $_POST["value"]["price"]) {
                        $update["compare_price"] = $_POST["value"]["compare_price"] * 100;

                        array_push($data["alerts"], [
                          "class" => "success",
                          "message" => "Product compare price updated"
                        ]);
                    } else {
                        array_push($data["alerts"], [
                          "class" => "warning",
                          "message" => "Product compare price cannot be less than the display price"
                        ]);
                    }
                } else {
                    array_push($data["alerts"], [
                      "class" => "warning",
                      "message" => "Product compare price cannot be less than $0.00"
                    ]);
                }
            }

            if (isset($_POST["value"]["quantity"]) && isset($_POST["original_quantity"])) {
                if ($_POST["value"]["quantity"] >= 0 && is_numeric($_POST["value"]["quantity"])) {
                    if ($_POST["value"]["quantity"] !== $_POST["original_quantity"]) {
                        $update["quantity"] = $_POST["value"]["quantity"];

                        array_push($data["alerts"], [
                          "class" => "success",
                          "message" => "Product inventory updated"
                        ]);
                    }
                } else {
                    array_push($data["alerts"], [
                      "class" => "warning",
                      "message" => "Product inventory must be a number equal to or greater than zero"
                    ]);
                }
            }

            $data["product"] = array_merge($data["product"], $update);

            if ($update) {
                $db->update("shop_items", $update, ["id" => $data["product"]["id"]]);
            }
        }
    }
}
