<?php
$logoutSuccess = $_POST["csrf"] ?? false;
if (!$app->admin || $logoutSuccess !== $app->admin["csrf"]) {
    $logoutSuccess = false;
}

if ($logoutSuccess) {
    Admin::logout();
}

header("Location: /admin/login" . ($logoutSuccess ? "?msg=loggedout" : ""));
exit;
