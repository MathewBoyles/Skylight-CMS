<?php
$data["pages"] = $db->select("pages", ["id", "alias", "title", "template", "published", "protected"]);

if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "deleted") {
        array_push($data["alerts"], [
            "class" => "success",
            "message" => "Page deleted"
        ]);
    }
}
