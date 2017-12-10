<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["user"] = false;

if (isset($_GET["id"])) {
    $data["user"] = $db->select("users", "*", ["id" => $_GET["id"]]);
    $data["user"] = $data["user"][0] ?? false;

    if ($data["user"]) {
        if ($data["user"]["id"] !== $app->admin["id"]) {
            if (isset($_POST["delete"]) && $csrfOkay) {
                $db->delete("users", ["id" => $_GET["id"]]);

                header("Location: /admin/users?msg=deleted");
                exit;
            }
        }
    }
}
