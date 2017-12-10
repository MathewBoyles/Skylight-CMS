<?php
define("PAGE_ID", "index");
require_once(__DIR__ . "/../config/require.php");

$aliasing = $aliasing ?? false;
$page = false;

if (!$aliasing) {
    $page = $db->select("pages", "*", ["alias" => $app->alias()]);
    $page = $page[0] ?? false;
}

$pageArray = array(
  "editor" => "",
  "id" => "",
  "title" => "",
  "theme" => $app->theme,
  "page" => array()
);

if ($aliasing) {
    $pageArray["title"] = $aliasing["title"];

    echo Twig::theme($aliasing["template"], $pageArray);
} elseif ($page) {
    $pageArray["id"] = $page["id"];
    $pageArray["title"] = $page["title"];
    $pageArray["page"] = json_decode($page["data"], true);

    $pageArray["post"] = $_POST;
    $pageArray["get"] = $_GET;

    if (isset($_GET["editor"])) {
        if ($_GET["editor"] == "true" && $app->admin) {
            $pageArray["editor"] = Twig::editor($pageArray);
        }
    }

    $pageArray = $app->pageArray($pageArray);
    $data = $pageArray["page"];

    if (isset($app->themeConfig["pages"][$page["template"]]["middleware"])) {
        foreach ($app->themeConfig["pages"][$page["template"]]["middleware"] as $file) {
            $fileName = $app->dir("html/assets/themes/" . $app->theme . "/middleware/") . $file;
            if (file_exists($fileName)) {
                require_once($fileName);
            }
        }
    }

    $pageArray["page"] = $data;

    echo Twig::theme([$page["template"], "page"], $pageArray);
} else {
    header("HTTP/1.0 404 Not Found");
    $pageArray["title"] = "404";

    echo Twig::theme("404", $pageArray);
}
