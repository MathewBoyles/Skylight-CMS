<?php
header("HTTP/1.0 500 Internal Server Error");

$debug = file_get_contents(__DIR__ . "/debug.json");
$debug = json_decode($debug, true);
$debug = !!$debug[0];
if (!$debug) {
    $error = "Site admin? Enable debugging to display errors.";
}

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Critical Error</title>

        <style>
        @import url("https://fonts.googleapis.com/css?family=Raleway:100,600");

        html, body {
            background: #FFF;
            color: #000;
            font-family: "Raleway", sans-serif;
            margin: 0;
        }

        .content {
          left: 50%;
          position: absolute;
          text-align: center;
          top: 50%;
          transform: translate(-50%, -50%);
          white-space: pre-line;
          width: 100%;
        }

        .title {
          font-size: 2em;
          margin-bottom: 10px;
        }

        .title, .message {
          padding: 0 10px;
        }
        </style>
    </head>
    <body>
      <div class="content">
        <div class="title">An error has occurred.</div>
        <div class="message"><?=$error; ?></div>
      </div>
    </body>
</html>
<?php die; ?>
