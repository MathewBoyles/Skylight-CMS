<?php
$data["users"] = $db->select("users", "*");

if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "deleted") {
        array_push($data["alerts"], [
          "class" => "success",
          "message" => "User deleted"
      ]);
    }

    if ($_GET["msg"] == "created") {
        array_push($data["alerts"], [
          "class" => "success",
          "message" => "Account created"
      ]);
    }
}
