<?php
$data["theme"] = $app->themeConfig;

$data["new_orders"] = $db->count("shop_orders", ["fulfilled" => "0"]);
$data["new_donations"] = $db->count("donations", ["seen" => "0"]);
$data["new_messages"] = $db->count("form_submissions", ["form" => "contact", "seen" => "0"]);
