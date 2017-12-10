<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["page"] = false;

if (isset($_GET["id"])) {
    $data["page"] = $db->select("pages", ["id", "alias", "title", "template", "published", "protected"], ["id" => $_GET["id"]]);
    $data["page"] = $data["page"][0] ?? false;

    if ($data["page"]) {
        if (!$data["page"]["protected"]) {
            if (isset($_POST["title"]) && isset($_POST["alias"]) && isset($_POST["template"]) && $csrfOkay) {
                $update = [];

                if (!$_POST["title"]) {
                    array_push($data["alerts"], [
                        "class" => "warning",
                        "message" => "Page title required"
                    ]);
                } elseif ($_POST["title"] !== $data["page"]["title"]) {
                    $update["title"] = $_POST["title"];
                    $data["page"]["title"] = $_POST["title"];

                    array_push($data["alerts"], [
                        "class" => "success",
                        "message" => "Page title updated"
                    ]);
                }

                if ($_POST["alias"] !== $data["page"]["alias"]) {
                    if (!preg_match("/^[a-zA-Z0-9_-]{3,64}$/", $_POST["alias"])) {
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
                        } else {
                            $update["alias"] = $_POST["alias"];
                            $data["page"]["alias"] = $_POST["alias"];

                            array_push($data["alerts"], [
                                "class" => "success",
                                "message" => "Page link updated"
                            ]);
                        }
                    }
                }

                if ($_POST["template"] !== $data["page"]["template"]) {
                    foreach ($data["theme"]["pages"] as $templateID => $template) {
                        if ($_POST["template"] == $templateID && $template["selectable"]) {
                            $update["template"] = $templateID;
                            $data["page"]["template"] = $templateID;

                            array_push($data["alerts"], [
                                "class" => "success",
                                "message" => "Page template updated"
                            ]);

                            break;
                        }
                    }
                }

                if ($update) {
                    $db->update("pages", $update, ["id" => $_GET["id"]]);
                }
            }
        }
    }
}
