<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["order"] = false;

if (isset($_GET["id"])) {
    $data["order"] = $db->select("shop_orders", "*", ["id" => $_GET["id"]]);
    $data["order"] = $data["order"][0] ?? false;

    if ($data["order"]) {
        $data["order"]["user"] = json_decode($data["order"]["user"], true);

        $items = json_decode($data["order"]["items"], true);
        $data["order"]["items"] = [];

        foreach ($items as $itemID => $item) {
            array_push($data["order"]["items"], [
              "item" => Shop::item($itemID, true),
              "price" => $item["price"],
              "quantity" => $item["quantity"]
            ]);
        }

        if ($csrfOkay && !$data["order"]["fulfilled"] && ($_POST["fulfilled"] ?? "") == "1") {
            $data["order"]["fulfilled"] = true;
            $db->update("shop_orders", [
              "fulfilled" => "1"
            ], [
              "id" => $data["order"]["id"]
            ]);
            $data["new_orders"] -= 1;

            array_push($data["alerts"], [
              "class" => "success",
              "message" => "Marked as fulfilled"
            ]);
        }
    }
}
