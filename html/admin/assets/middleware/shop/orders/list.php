<?php
$data["orders"] = $db->select("shop_orders", "*", [
  "ORDER" => [
    "fulfilled" => "ASC",
    "id" => "ASC"
  ]
]);

foreach ($data["orders"] as $i => $donation) {
    $data["orders"][$i]["user"] = json_decode($data["orders"][$i]["user"], true);
}
