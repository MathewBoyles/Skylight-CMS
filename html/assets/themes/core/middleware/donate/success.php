<?php
if (!$pageArray["editor"] && isset($_POST["amount"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["phone"])) {
    if (is_numeric($_POST["amount"])) {
        $db->insert("donations", [
          "ip" => $app->ip,
          "amount" => $_POST["amount"] * 100,
          "user" => json_encode([
            "name" => $_POST["name"],
            "email" => $_POST["email"],
            "phone" => $_POST["phone"]
          ], true),
          "timestamp" => time()
        ]);
    }
}
