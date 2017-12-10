<?php
if (!$pageArray["editor"]) {
    $item = $_GET["id"] ?? false;

    if ($item) {
        $item = Shop::item($_GET["id"]);
        $data["item"] = $item;

        if ($item) {
            $pageArray["title"] = $item["title"];
        }
    }

    if (!$item) {
        $pageArray["title"] = "404";
        header("HTTP/1.0 404 Product Not Found");
        echo Twig::theme("404", $pageArray);
        die;
    }
}
