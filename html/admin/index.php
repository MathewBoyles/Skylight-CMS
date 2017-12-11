<?php
define("PAGE_ID", "admin/index");
require_once(__DIR__ . "/../../config/require.php");

$adminData = json_decode(file_get_contents($app->dir("html/admin") . "/admin.json"), true);

$pageID = $app->alias() == "admin" ? "index" : substr($app->alias(), 6);
$page = $adminData["pages"][$pageID] ?? [];

if (!is_array($page)) {
    $page = $adminData["pages"][$adminData["pages"][$pageID]] ?? false;
}

$pageArray = [];
$pageArray["links"] = $adminData["links"];

if ($page) {
    if (!$app->admin && $pageID !== "login") {
        header("Location: /admin/login");
        exit;
    }

    $pageArray["page"] = [];
    $pageArray["page"]["id"] = $pageID;
    $pageArray["page"]["active"] = $page["active"];

    $data = $pageArray["page"];

    foreach ($page["middleware"] as $file) {
        $fileName = $app->dir("html/admin/assets/middleware/") . $file;
        if (file_exists($fileName)) {
            require_once($fileName);
        }
    }

    $pageArray["page"] = $data;

    echo Twig::admin($page["template"], $pageArray);
} else {
    header("HTTP/1.0 404 Not Found");

    echo Twig::theme("404", ["title" => "404", "theme" => $app->theme]);
}
