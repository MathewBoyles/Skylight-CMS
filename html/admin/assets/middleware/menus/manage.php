<?php
$requireReload = false;

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["menu"] = $app->themeConfig["navs"][$_GET["id"] ?? ""] ?? false;
if ($data["menu"]) {
    $data["menu"]["id"] = $_GET["id"];
    $data["menu"]["links"] = $app->navs[$_GET["id"]] ?? [];

    if ($csrfOkay) {
        if (isset($_POST["sort"])) {
            if (is_array($_POST["sort"])) {
                $requireReload = true;

                $i = 0;
                foreach ($_POST["sort"] as $id) {
                    $db->update("nav_links", [
                      "order" => $i
                    ], [
                      "id" => $id
                    ]);
                    $i++;
                }
            }
        }

        if (isset($_POST["remove"])) {
            if (is_array($_POST["remove"])) {
                $requireReload = true;

                foreach ($_POST["remove"] as $id) {
                    $db->delete("nav_links", [
                      "id" => $id
                    ]);
                }
            }
        }

        if (isset($_POST["title"]) && isset($_POST["link"])) {
            if ($_POST["title"] && $_POST["link"]) {
                $requireReload = true;

                $db->insert("nav_links", [
                  "nav" => $_GET["id"],
                  "theme" => $app->theme,
                  "title" => $_POST["title"],
                  "type" => "link",
                  "link" => $_POST["link"],
                  "external" => ($_POST["external"] ?? 0) == "1" ? "1" : "0",
                  "order" => count($data["menu"]["links"])
                ]);
            }
        }
    }

    if ($requireReload) {
        header("Location: /admin/menus/manage?id=" . $_GET["id"]);
        exit;
    }
}
