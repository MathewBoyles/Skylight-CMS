<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["messages"] = $db->select("form_submissions", "*", ["form" => "contact"]);

foreach ($data["messages"] as $i => $message) {
    $data["messages"][$i]["data"] = json_decode($data["messages"][$i]["data"], true);

    if (($_POST["delete"] ?? "") == $message["id"] && $csrfOkay) {
      unset($data["messages"][$i]);

      array_push($data["alerts"], [
        "class" => "success",
        "message" => "Message deleted"
      ]);

      $db->delete("form_submissions", ["id" => $message["id"]]);
    }
}
