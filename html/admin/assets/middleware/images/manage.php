<?php
$data["alerts"] = [];

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

if (isset($_FILES["file"]) && $csrfOkay) {
    $imageTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif"];

    if (!in_array($_FILES["file"]["type"], $imageTypes) || $_FILES["file"]["size"] > 5242880) {
        array_push($data["alerts"], [
          "class" => "warning",
          "message" => "Invalid image (JPEG/JPG, PNG and GIF only; max 5MB)"
        ]);
    } elseif (file_exists($app->dir("html/assets/uploads/") . $_FILES["file"]["name"])) {
        array_push($data["alerts"], [
          "class" => "warning",
          "message" => "Image with the same name already exists"
        ]);
    } else {
        array_push($data["alerts"], [
          "class" => "success",
          "message" => "Image uploaded"
        ]);

        $img = $image->make($_FILES["file"]["tmp_name"]);
        $img->save($app->dir("html/assets/uploads/") . $_FILES["file"]["name"]);

        $img->fit(300, 300);
        $img->save($app->dir("html/assets/uploads/thumbnail/") . $_FILES["file"]["name"]);
    }
}

if (isset($_POST["delete"]) && $csrfOkay) {
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
                  if ($_POST["delete"] == $file) {
                      array_push($data["alerts"], [
                        "class" => "success",
                        "message" => "Image delete"
                      ]);

                      unlink($app->dir("html/assets/uploads/") . $file);
                      break;
                  }
              }
      }
    }
}
