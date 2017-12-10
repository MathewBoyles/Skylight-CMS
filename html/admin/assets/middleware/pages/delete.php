<?php
$data["page"] = false;
$data["alerts"] = array();

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

if (isset($_GET["id"])) {
    $data["page"] = $db->select("pages", ["id", "title", "protected"], ["id" => $_GET["id"]]);
    $data["page"] = $data["page"][0] ?? false;

    if ($data["page"]) {
        if (!$data["page"]["protected"]) {
            if (isset($_POST["delete"]) && $csrfOkay) {
                $db->delete("pages", ["id" => $_GET["id"]]);

                header("Location: /admin/pages?msg=deleted");
                exit;
            }
        }
    }
}
