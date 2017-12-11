<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["user"] = false;

if (isset($_GET["id"])) {
    $data["user"] = $db->select("users", "*", ["id" => $_GET["id"]]);
    $data["user"] = $data["user"][0] ?? false;

    if ((!$data["user"]["su"] || $app->admin["su"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["password_confirm"]) && $csrfOkay) {
        $update = [];

        if (!$_POST["name"]) {
            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Name required"
            ]);
        } elseif ($_POST["name"] !== $data["user"]["name"]) {
            $update["name"] = $_POST["name"];

            array_push($data["alerts"], [
              "class" => "success",
              "message" => "Name changed"
            ]);
        }

        if ($_POST["email"] !== $data["user"]["email"]) {
            if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $emailUsed = $db->select("users", [
                  "email" => $_POST["email"]
                ]);
                $emailUsed = $emailUsed[0]["id"] ?? false;

                if ($emailUsed && $emailUsed !== $app->admin["email"]) {
                    array_push($data["alerts"], [
                      "class" => "warning",
                      "message" => "Email already in use"
                    ]);
                } else {
                    $update["email"] = $_POST["email"];

                    array_push($data["alerts"], [
                      "class" => "success",
                      "message" => "Email changed"
                    ]);
                }
            } else {
                array_push($data["alerts"], [
                  "class" => "warning",
                  "message" => "Invalid email address"
                ]);
            }
        }

        if ($_POST["password"] && $_POST["password"] !== $_POST["password_confirm"]) {
            array_push($data["alerts"], [
              "class" => "warning",
              "message" => "Passwords do not match"
            ]);
        } elseif ($_POST["password"]) {
            $update["password"] = Admin::hash($_POST["password"]);

            array_push($data["alerts"], [
              "class" => "success",
              "message" => "Password changed"
            ]);
        }

        if ($update) {
            $db->update("users", $update, [
              "id" => $data["user"]["id"]
            ]);

            $data["user"] = array_merge($data["user"], $update);
        }
    }
}
