<?php
class Session {
  public static function set($name, $value) {
    $_SESSION[$name] = $value;
  }

  public static function delete($name) {
    unset($_SESSION[$name]);
  }

  public function remove($name) {
    Session::delete($name);
  }

  public static function get($name) {
    return $_SESSION[$name] ?? false;
  }
}
