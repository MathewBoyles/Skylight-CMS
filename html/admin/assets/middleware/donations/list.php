<?php
$data["donations"] = $db->select("donations", "*");

foreach ($data["donations"] as $i => $donation) {
    $data["donations"][$i]["user"] = json_decode($data["donations"][$i]["user"], true);
}

$db->update("donations", ["seen" => "1"], ["seen" => "0"]);
