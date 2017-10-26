<?php
session_start();
require "config.php";
require_once "Builder.php";
require_once "./dibi/loader.php";

$build = new Builder();
$db = new DibiConnection($options);

if (isset($_POST["hours"]) && is_numeric($_POST["hours"]) && $_POST["hours"] >= 0) {
  $date = new DateTime();
  $date->sub(new DateInterval('PT'.$_POST["hours"].'H'));
  $deleted = $db->query('DELETE FROM history WHERE date <= ?', $date);
}


$build->head();
$build->menu();
$build->containerMain();

$build->history_delete_form($deleted);

$build->containerMainEnd();
$build->footer();
?>
