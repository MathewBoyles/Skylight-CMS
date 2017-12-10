<?php
class Cart {
  public static function list($was = false)
  {
    $list = Session::get($was ? ("cart_was_" . $was) : "cart");
    $list = $list ? $list : [];
    $return = array(
      "items" => array(),
      "count" => 0,
      "total" => 0
    );

    foreach ($list as $item => $quantity) {
      $data = Shop::item($item);
      if ($data) {
        if ($quantity > $data["quantity"] && $data["quantity"] >= 0 && !$was) {
          $quantity = $data["quantity"];
          Cart::set($item, $quantity);
        }

        $return["items"][$item] = [
          "quantity" => $quantity,
          "item" => $data
        ];
        $return["count"] += $quantity;
        $return["total"] += $quantity * $data["price"];
      }
    }

    return $return;
  }

  public static function set($id, $quantity)
  {
    $item = Shop::item($id);

    if ($item) {
      if ($quantity <= 0) {
        Cart::remove($id);
      }

      $list = Session::get("cart");

      if ($quantity > 0) {
        $list[$id] = $quantity;
      }

      Session::set("cart", $list);
    }
  }

  public static function add($id)
  {
    $items = Cart::list();

    $quantity = $items["items"][$id]["quantity"] ?? 0;
    $quantity++;

    Cart::set($id, $quantity);
  }

  public static function remove($id)
  {
    $items = Shop::item($id);

    if (isset($item[$id])) {
      unset($items[$id]);
    }

    Session::set("cart", $items);
  }

  public static function purchase($data)
  {
    global $app, $db;

    $key = rand(1000, 9999) . time();
    Cart::save($key);

    $items = array();

    $list = Cart::list();
    foreach ($list["items"] as $itemID => $item) {
      $db->update("shop_items", [
        "sold" => $item["item"]["sold"] + $item["quantity"],
        "quantity" => $item["item"]["quantity"] - $item["quantity"]
      ], [
        "id" => $itemID
      ]);

      $items[$itemID] = [
        "quantity" => $item["quantity"],
        "price" => $item["item"]["price"]
      ];
    }

    $userData = [
      "name" => $data["name"] ?? "",
      "email" => $data["email"] ?? "",
      "phone" => $data["phone"] ?? "",
      "address_line1" => $data["address_line1"] ?? "",
      "address_line2" => $data["address_line2"] ?? "",
      "city" => $data["city"] ?? "",
      "region" => $data["region"] ?? "",
      "post_code" => $data["post_code"] ?? "",
      "country" => $data["country"] ?? ""
    ];

    $db->insert("shop_orders", [
      "key" => $key,
      "ip" => $app->ip,
      "items" => json_encode($items, true),
      "user" => json_encode($userData, true),
      "total" => $list["total"],
      "timestamp" => time()
    ]);

    Cart::clear();

    return $key;
  }

  public static function save($key)
  {
    Session::set("cart_was_" . $key, Session::get("cart"));
  }

  public static function clear()
  {
    Session::set("cart", []);
  }
}
