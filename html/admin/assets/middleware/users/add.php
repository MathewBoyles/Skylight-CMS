<?php
$data["alerts"] = array();

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["password_confirm"]) && $csrfOkay) {
    if (!$_POST["name"]) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Name required"
        ]);
    }

    if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailUsed = $db->count("users", [
          "email" => $_POST["email"]
        ]);

        if ($emailUsed) {
            array_push($data["alerts"], [
                "class" => "warning",
                "message" => "Email already in use"
            ]);
        }
    } else {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Invalid email address"
        ]);
    }

    if (!$_POST["password"]) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Password required"
        ]);
    } elseif ($_POST["password"] !== $_POST["password_confirm"]) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Passwords do not match"
        ]);
    }

    if (!$data["alerts"]) {
        Admin::create($_POST["email"], $_POST["password"], [
          "name" => $_POST["name"]
        ]);

        header("Location: /admin/users?msg=created");
        exit;
    }
}
