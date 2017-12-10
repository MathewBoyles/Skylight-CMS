<?php
$data["message"] = $db->select("form_submissions", "*", ["id" => $_GET["id"] ?? ""]);

$data["message"] = $data["message"][0] ?? false;
if ($data["message"]) {
    $data["message"]["data"] = json_decode($data["message"]["data"], true);

    if (!$data["message"]["seen"]) {
        $db->update("form_submissions", [
          "seen" => "1"
        ], [
          "id" => $data["message"]["id"]
        ]);

        if ($data["message"]["form"] == "contact") {
            $data["new_messages"] -= 1;
        }
    }
}
