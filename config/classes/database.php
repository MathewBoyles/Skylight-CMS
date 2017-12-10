<?php
use Medoo\Medoo;

try {
    $db = new Medoo([
        "database_type" => "mysql",
        "database_name" => DATABASE_DATABASE,
        "server" => DATABASE_HOSTNAME,
        "username" => DATABASE_USERNAME,
        "password" => DATABASE_PASSWORD,
        "prefix" => DATABASE_PREFIX
    ]);
} catch (Exception $e) {
    $app->error($e->getMessage());
}
