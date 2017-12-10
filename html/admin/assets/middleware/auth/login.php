<?php
$messages = array(
  "loggedout" => array(
    "class" => "success",
    "message" => "You have been logged out."
  ),
  "error" => array(
    "class" => "danger",
    "message" => "Invalid email/password combination."
  )
);

$data["message"] = $messages[$_GET["msg"] ?? ""] ?? false;

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $user = Admin::check($_POST["email"], $_POST["password"]);

    if ($user) {
        Admin::login($user["id"], $user["key"]);
        header("Location: /admin/index");
    } else {
        header("Location: /admin/login?msg=error");
    }

    exit;
}
