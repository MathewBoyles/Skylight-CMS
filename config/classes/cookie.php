<?php
class Cookie
{
    public static function set($name, $value, $expiry = true)
    {
        setCookie($name, $value, ($expiry ? strtotime($expiry) : false), "/");
    }

    public static function delete($name)
    {
        setCookie($name, "", strtotime("-1 week"), "/");
        setCookie($name, "", strtotime("-1 week"));
    }

    public function remove($name)
    {
        Cookie::delete($name);
    }

    public static function get($name)
    {
        return $_COOKIE[$name] ?? false;
    }
}
