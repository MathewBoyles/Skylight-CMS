<?php
class Twig
{
    public static function admin($page, $pageArray)
    {
        global $app;

        $content = Twig::render($app->dir("html/admin/assets/templates/"), $page, $pageArray);
        if (!$content) {
            $app->error("Unable to load admin theme file.");
        }

        return $content;
    }

    public static function theme($page, $pageArray)
    {
        global $app;

        $content = Twig::render($app->dir("html/assets/themes/" . $app->theme . "/"), $page, $pageArray);
        if (!$content) {
            $app->error("Unable to load Twig theme file.");
        }

        return $content;
    }

    public static function snippit($page, $pageArray)
    {
        global $app;

        $content = Twig::render($app->dir("html/assets/snippits/"), $page, $pageArray);

        return $content;
    }

    public static function editor($pageArray)
    {
        return Twig::snippit("editor", $pageArray) . "\n" . Twig::theme("snippits/editor", $pageArray);
    }

    private static function render($dirName, $fileName, $pageArray)
    {
        global $app;

        if (is_array($fileName)) {
            $fileNames = $fileName;
            $fileName = "";

            foreach ($fileNames as $file) {
                if (file_exists($dirName . $file . ".twig.html")) {
                    $fileName = $file;
                    break;
                }
            }
        }

        $fileName .= ".twig.html";

        $loader = new Twig_Loader_Filesystem($dirName);
        $twig = new Twig_Environment($loader, ["cache" => $app->debug ? false : $app->dir("config/cache")]);
        $twig->addExtension(new Twig_Extension_StringLoader());

        return file_exists($dirName . $fileName) ? $twig->render($fileName, $app->pageArray($pageArray)) : "";
    }
}
