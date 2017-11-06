<?php
session_start();
require "./config.php";
require_once "./Builder.php";
require_once "./dibi/loader.php";

$build = new Builder();
$db = new DibiConnection($options);

if (isset($_GET["p"]) && is_numeric($_GET["p"])) {
  $p = $_GET["p"];
} else {
  $p = 0;
}

$build->head();
$build->menu();
$build->containerMain();

$history = $db->query('SELECT * FROM history ORDER BY date DESC');

$build->history_navigator($history, $p);
$build->history_container($history, $p);

$build->containerMainEnd();
$build->footer();
?>
