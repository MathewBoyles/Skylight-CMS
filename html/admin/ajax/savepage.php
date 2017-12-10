<?php
  define("PAGE_ID", "admin/ajax/savepage");
  require_once(__DIR__ . "/../../../config/require.php");

  header("Content-Type: text/json");
  $response = [
    "status" => "success",
    "message" => "",
    "data" => []
  ];

  if ($app->admin) {
      $response["status"] = "error";
      $response["message"] = "Page does not exist.";

      if (isset($_POST["id"]) && isset($_POST["data"]) && isset($_POST["csrf"])) {
          if ($_POST["csrf"] != $app->admin["csrf"]) {
              $response["status"] = "error";
              $response["message"] = "CSRF token mismatch.";
          } elseif (is_numeric($_POST["id"]) && is_array($_POST["data"])) {
              $page = $db->select("pages", "*", ["id" => $_POST["id"]]);
              $page = $page[0] ?? false;

              if ($page) {
                  $response["status"] = "success";
                  $response["message"] = "";

                  $data = [];
                  if ($page["data"]) {
                      $data = json_decode($page["data"], true);
                  }
                  $data = array_merge($data, $_POST["data"]);
                  ksort($data);
                  $data = json_encode($data, true);

                  $title = $page["title"];
                  if (isset($_POST["data"]["master_title"])) {
                      $title =$_POST["data"]["master_title"];
                  }

                  $db->update("pages", [
                    "data" => $data,
                    "title" => $title
                  ], [
                    "id" => $page["id"]
                  ]);
              }
          }
      }
  } else {
      $response["status"] = "error";
      $response["message"] = "Not authorized.";
  }

  die(json_encode($response));
