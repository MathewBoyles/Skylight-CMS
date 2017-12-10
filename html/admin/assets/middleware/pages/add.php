<?php
$data["alerts"] = array();

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

if (isset($_POST["title"]) && isset($_POST["alias"]) && isset($_POST["template"]) && $csrfOkay) {
    $update = array();

    if (!$_POST["title"]) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Page title required"
        ]);
    }

    if (!$_POST["title"]) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Page link required"
        ]);
    } elseif (!preg_match("/^[a-zA-Z0-9_-]{3,64}$/", $_POST["alias"])) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Page link is invalid (letters, numbers, underscores and hyphens only; 3-64 characters only)"
        ]);
    } elseif (substr($_POST["alias"], 0, 1) == "_" || substr($_POST["alias"], 0, 1) == "-") {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Page link cannot begin with an underscore or hyphen"
        ]);
    } else {
        $isTaken = false;
        foreach ($reservedAlias as $reserved) {
            if (substr($_POST["alias"], 0, strlen($reserved)) == $reserved) {
                $isTaken = true;
            }
            if ($_POST["alias"] == substr($reserved, 0, strlen($reserved) - 1)) {
                $isTaken = true;
            }
        }

        if (!$isTaken) {
            $isTaken = $db->count("pages", [
            "alias" => $_POST["alias"]
          ]);
        }

        if ($isTaken) {
            array_push($data["alerts"], [
                "class" => "warning",
                "message" => "Page link already in use"
            ]);
        }
    }

    $pageTemplate = false;
    foreach ($data["theme"]["pages"] as $templateID => $template) {
        if ($_POST["template"] == $templateID && $template["selectable"]) {
            $pageTemplate = $templateID;

            break;
        }
    }

    if (!$pageTemplate) {
        array_push($data["alerts"], [
            "class" => "warning",
            "message" => "Page template invalid"
        ]);
    }

    if (!$data["alerts"]) {
        $db->insert("pages", [
          "title" => $_POST["title"],
          "alias" => $_POST["alias"],
          "template" => $pageTemplate
        ]);

        header("Location: /" . $_POST["alias"] . "?editor=true");
        exit;
    }
}
