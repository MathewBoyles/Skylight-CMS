<?php
$data["config"] = false;
$doTheme = $app->theme;

$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

if (isset($_GET["id"])) {
    $data["id"] = $_GET["id"];

    if ($_GET["id"] == "branding") {
        $doTheme = "system";

        $data["config"] = array(
          "title" => "Branding",
          "description" => "Site logo, title and description.",
          "options" => array(
            "logo" => array(
              "type" => "image",
              "title" => "Site logo",
              "required" => true,
              "value" => $app->config["logo"] ?? ""
            ),
            "title" => array(
              "type" => "input",
              "title" => "Site title",
              "required" => true,
              "value" => $app->config["title"] ?? ""
            ),
            "description" => array(
              "type" => "input",
              "title" => "Site description",
              "required" => false,
              "value" => $app->config["description"] ?? ""
            )
          )
        );
    } else {
        $data["config"] = $app->themeConfig["config"][$_GET["id"]] ?? false;

        if ($data["config"]) {
            $themeConfig = $app->themeConfig();

            foreach ($data["config"]["options"] as $configName => $configData) {
                $data["config"]["options"][$configName]["value"] = $themeConfig[$configName] ?? "";
            }
        }
    }

    if ($data["config"]) {
        if (isset($_POST["value"])) {
            if (is_array($_POST["value"]) && $csrfOkay) {
                $items = array();

                foreach ($data["config"]["options"] as $configName => $configData) {
                    $configValue = $_POST["value"][$configName] ?? "";

                    if ($configValue !== "" || !$configData["required"]) {
                        $data["config"]["options"][$configName]["value"] = $configValue;
                        $configWhere = array(
                          "theme" => $doTheme,
                          "name" => $configName
                        );

                        $isUpdate = $db->count("theme_options", $configWhere);

                        if ($isUpdate) {
                            $db->update("theme_options", [
                              "value" => $configValue
                            ], $configWhere);
                        } else {
                            $configWhere["value"] = $configValue;

                            $db->insert("theme_options", $configWhere);
                        }
                    }
                }
            }
        }
    }
}
