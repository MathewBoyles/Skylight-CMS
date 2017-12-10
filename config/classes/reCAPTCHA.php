<?php
class reCAPTCHA
{
    public static function verify($secret, $response)
    {
        $return = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $response);
        $return = json_decode($return, true);
        $return = $return["success"] ?? false;

        return $return;
    }
}
