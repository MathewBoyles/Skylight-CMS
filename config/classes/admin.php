<?php
class Admin
{
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function create($email, $password, $options = array())
    {
        global $db;

        $options["email"] = $email;
        $options["password"] = Admin::hash($password);
        $options["csrf"] = md5(rand(1000, 9000) . time());
        $options["key"] = md5(rand(1000, 9000) . time());

        $db->insert("users", $options);
    }

    public static function check($email, $password)
    {
        global $db;

        $return = false;

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $db->select("users", ["id", "key", "password"], ["email" => $email]);
            $user = $user[0] ?? false;

            if ($user) {
                if (password_verify($password, $user["password"])) {
                    $return = $user;
                    unset($return["password"]);
                }
            }
        }

        return $return;
    }

    public static function login($userID, $key = false)
    {
        $data = array(
      "id" => $userID
    );
        if ($key) {
            $data["key"] = $key;
        }

        Session::set("admin", $data);
    }

    public static function logout()
    {
        global $app;

        Session::delete("admin");
    }
}
