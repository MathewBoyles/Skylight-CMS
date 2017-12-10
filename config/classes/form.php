<?php
class Form
{
    public static function submit($form, $data)
    {
        global $app, $db;

        $data = json_encode($data, true);

        $db->insert("form_submissions", [
          "ip" => $app->ip,
          "form" => $form,
          "data" => $data,
          "timestamp" => time()
        ]);
    }
}
