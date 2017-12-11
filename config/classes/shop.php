<?php
class Shop
{
    public static function all($overrideActive = false)
    {
        global $db;

        $options = [
          "deleted" => "0"
        ];
        if (!$overrideActive) {
            $options["active"] = "1";
        }

        $items = $db->select("shop_items", "*", $options);

        return $items;
    }

    public static function item($id, $overrideDeleted = false)
    {
        global $db;

        $options = [
          "id" => $id
        ];
        if (!$overrideDeleted) {
            $options["deleted"] = "0";
        }

        $item = $db->select("shop_items", "*", $options);
        $item = $item[0] ?? [];

        return $item;
    }

    public static function set($id, $data)
    {
        global $db;

        $db->update("shop_items", $data, ["id" => $id]);
    }
}
