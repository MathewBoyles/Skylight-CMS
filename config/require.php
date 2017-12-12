<?php
function shutDownFunction()
{
    $errorData = error_get_last();
    if ($errorData) {
        $error = $errorData["message"];
        $error .= " <br /><small>Line ";
        $error .= $errorData["line"];
        $error .= " of ";
        $error .= $errorData["file"];
        $error .= "</small>";
        require_once("error.php");
    }
}
register_shutdown_function("shutDownFunction");

if (!file_exists(dirname(__DIR__) . "/vendor/autoload.php")) {
    $debug = true;
    $error = "Composer install required.";
    require_once("error.php");
}

if (!file_exists(dirname(__DIR__) . "/html/node_modules")) {
    $debug = true;
    $error = "Node install required.";
    require_once("error.php");
}

if (!file_exists(__DIR__ . "/database.php")) {
    $debug = true;
    $error = "Database config install required.";
    require_once("error.php");
}

require_once(dirname(__DIR__) . "/vendor/autoload.php");
require_once(__DIR__ . "/database.php");

session_name("CMS_PRESENSE");
session_start();

foreach (glob(__DIR__ . "/classes/*.php") as $filename) {
    try {
        require_once($filename);
    } catch (Exception $e) {
        $error = $e->getMessage();
        require_once("error.php");
    }
}

use Intervention\Image\ImageManager;

$image = new ImageManager();

$app = new App;

if (!file_exists($app->dir("html/assets/uploads/"))) {
    mkdir($app->dir("html/assets/uploads/"), 0777);
}

if (!file_exists($app->dir("html/assets/uploads/thumbnail/"))) {
    mkdir($app->dir("html/assets/uploads/thumbnail/"), 0777);
}
