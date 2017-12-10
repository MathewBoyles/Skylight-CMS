<?php
  define("PAGE_ID", "admin/ajax/getimages");
  require_once(__DIR__ . "/../../../config/require.php");

  header("Content-Type: text/json");
  $response = [
    "status" => "success",
    "message" => "",
    "data" => []
  ];

  if ($app->admin) {
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
                      array_push($response["data"], $file);
                  }
          }
      }
  } else {
      $response["status"] = "error";
      $response["message"] = "Not authorized.";
  }

  die(json_encode($response));
