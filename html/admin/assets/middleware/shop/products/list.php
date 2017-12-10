<?php
$data["products"] = Shop::all(true);

if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "deleted") {
        array_push($data["alerts"], [
            "class" => "success",
            "message" => "Product deleted"
        ]);
    }

    if ($_GET["msg"] == "created") {
        array_push($data["alerts"], [
            "class" => "success",
            "message" => "Product created"
        ]);
    }
}
