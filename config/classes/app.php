<?php
class App
{
    private $tmp = [];
    public function error($error)
    {
        require_once($this->dir("config") . "/error.php");
    }

    public function pageArray($pageArray)
    {
        global $db;

        if (!isset($pageArray["_pageArray"])) {
            $pageArray["_pageArray"] = true;
            $pageArray["year"] = date("Y");

            $pageArray["admin"] = $this->admin;
            $pageArray["site"] = $this->config;
            $pageArray["cart"] = Cart::list();
            $pageArray["navs"] = $this->navs;
            $pageArray["alias"] = $this->alias();

            $pageArray["config"] = $this->themeConfig($pageArray["theme"] ?? false);
        }

        return $pageArray;
    }

    public function themeConfig($theme = false)
    {
        global $db;

        if (!$theme) {
            $theme = $this->theme;
        }

        $return = [];

        if ($this->tmp[$theme . "_themeOptions"] ?? false) {
            $return = $this->tmp[$theme . "_themeOptions"];
        } else {
            foreach ($this->themeConfig["config"] as $configOptionGroup => $configOptionData) {
                foreach ($configOptionData["options"] as $configOption => $configOptionInfo) {
                    $return[$configOption] = $configOptionInfo["default"];
                }
            }

            $configOptions = $db->select("theme_options", "*", ["theme" => $theme]);
            foreach ($configOptions as $configOption) {
                $return[$configOption["name"]] = $configOption["value"];
            }

            $this->tmp[$theme . "_themeOptions"] = $return;
        }

        return $return;
    }

    public function alias()
    {
        $page = $_SERVER["REQUEST_URI"];
        $page = explode("?", $page);
        $page = $page[0];
        $page = explode("/", $page, 2);
        $page = $page[1];

        if (substr($page, -1) == "/") {
            $page = substr($page, 0, -1);
        }

        return $page;
    }

    public function dir($dir = "")
    {
        return dirname(dirname(__DIR__)) . "/" . $dir;
    }

    public function __construct()
    {
        global $db;
        $this->ip = $_SERVER["REMOTE_ADDR"];

        $this->admin = Session::get("admin") ?? false;
        if (is_array($this->admin)) {
            $this->admin = $db->select("users", ["id", "email", "name", "csrf", "su"], $this->admin);
            $this->admin = $this->admin[0] ?? false;
        } elseif ($this->admin) {
            $this->admin = false;
            Session::delete("admin");
        }

        $this->debug = file_get_contents($this->dir("config") . "/debug.json");
        $this->debug = json_decode($this->debug, true);
        $this->debug = !!$this->debug[0];

        $this->config = [];
        $configOptions = $db->select("theme_options", "*", ["theme" => "system"]);
        foreach ($configOptions as $configOption) {
            $this->config[$configOption["name"]] = $configOption["value"];
        }

        $this->theme = $this->config["theme"] ?? "core";

        $this->themeConfig = [];
        if (file_exists($this->dir("html/assets/themes/" . $this->theme) . "/theme.json")) {
            $this->themeConfig = file_get_contents($this->dir("html/assets/themes/" . $this->theme) . "/theme.json");
            $this->themeConfig = json_decode($this->themeConfig, true);
        } elseif (substr(PAGE_ID, 0, 6) !== "admin/") {
            $this->error("Selected theme <em>" . $this->theme . "</em> is not installed.");
        }

        $this->navs = [];
        $navLinks = $db->select("nav_links", "*", ["theme" => $this->theme, "ORDER" => ["order" => "ASC", "id" => "ASC"]]);
        foreach ($navLinks as $navLink) {
            if (!isset($this->navs[$navLink["nav"]])) {
                $this->navs[$navLink["nav"]] = [];
            }

            $navItem = $navLink;
            $navItem["active"] = ("/" . $this->alias()) == $navItem["link"];

            array_push($this->navs[$navLink["nav"]], $navItem);
        }
    }
}
