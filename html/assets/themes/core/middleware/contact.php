<?php
$data["has_success"] = false;
$data["has_error"] = false;

if (!$pageArray["editor"]) {
    if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["subject"]) && isset($_POST["message"]) && isset($_POST["g-recaptcha-response"])) {
        if (!$_POST["name"] || !$_POST["email"] || !$_POST["subject"] || !$_POST["message"]) {
            $data["has_error"] = "Please fill all required fields.";
        } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $data["has_error"] = "Email address is invalid.";
        } else {
            $recaptcha = $_POST["g-recaptcha-response"] ? reCAPTCHA::verify($pageArray["config"]["recaptcha_secret"], $_POST["g-recaptcha-response"]) : false;
            if ($recaptcha) {
                Form::submit("contact", [
                  "name" => $_POST["name"],
                  "email" => $_POST["email"],
                  "subject" => $_POST["subject"],
                  "message" => $_POST["message"]
                ]);

                $data["has_success"] = true;
            } else {
                $data["has_error"] = "reCAPTCHA failed.";
            }
        }
    }
}
