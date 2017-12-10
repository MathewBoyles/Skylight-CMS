<?php
$csrfOkay = $_POST["csrf"] ?? false;
$csrfOkay = $csrfOkay == $app->admin["csrf"];

$data["config"] = false;
$doTheme = $app->theme;

if (isset($_GET["id"])) {
    $data["id"] = $_GET["id"];

    if ($_GET["id"] == "branding") {
        $doTheme = "system";

        $data["config"] = [
          "title" => "Branding",
          "description" => "Site logo, title and description.",
          "options" => [
            "logo" => [
              "type" => "image",
              "title" => "Site logo",
              "required" => true,
              "value" => $app->config["logo"] ?? ""
            ],
            "title" => [
              "type" => "input",
              "title" => "Site title",
              "required" => true,
              "value" => $app->config["title"] ?? ""
            ],
            "description" => [
              "type" => "input",
              "title" => "Site description",
              "required" => false,
              "value" => $app->config["description"] ?? ""
            ]
          ]
        ];
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
                $items = [];

                foreach ($data["config"]["options"] as $configName => $configData) {
                    $configValue = $_POST["value"][$configName] ?? "";

                    if ($configValue !== "" || !$configData["required"]) {
                        $data["config"]["options"][$configName]["value"] = $configValue;
                        $configWhere = [
                          "theme" => $doTheme,
                          "name" => $configName
                        ];

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
