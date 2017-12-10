<?php
$data["images"] = [];

$fileScan = scandir($app->dir("html/assets/uploads/"));
foreach ($fileScan as $file) {
    switch ($file) {
        case ".":
          break;
        case "..":
          break;
        case "thumbnail":
          break;
        default:
            $imageData = getimagesize($app->dir("html/assets/uploads/") . $file);

            if ($imageData) {
                array_push($data["images"], $file);
            }
    }
}
