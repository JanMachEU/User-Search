<?php
session_start();
require_once "./dibi/loader.php";

echo "<!DOCTYPE html>
<html>
  <head>
    <meta charset='utf-8'>
    <title>Instalation</title>
  </head>
  <body>";

if (isset($_GET["step"]) && is_numeric($_GET["step"])) {
  $step = $_GET["step"];
} else {
  $step = 0;
}
if (isset($_POST["api_token"])) {
  if ($_POST["api_token"] !== "") {
    $_SESSION["api_token"] = $_POST["api_token"];
  }
}
if (isset($_POST["db_host"])) {
  if ($_POST["db_host"] !== "") {
    $_SESSION["db_host"] = $_POST["db_host"];
  }
}
if (isset($_POST["db_database"])) {
  if ($_POST["db_database"] !== "") {
    $_SESSION["db_database"] = $_POST["db_database"];
  }
}
if (isset($_POST["db_username"])) {
  if ($_POST["db_username"] !== "") {
    $_SESSION["db_username"] = $_POST["db_username"];
  }
}
if (isset($_POST["db_password"])) {
  if ($_POST["db_password"] !== "") {
    $_SESSION["db_password"] = $_POST["db_password"];
  }
}
if ($step == 0) {
  // Default state. Creates form.

  $form = '<form class="" action="install.php?step=1" method="post">';
    $form .= '<p>API Settings</p>';
      $form .= '<label for="api_token">GitHub API Token</label> ';
      $form .= '<input type="text" name="api_token" value="' . $_SESSION["api_token"] . '" required>';
    $form .= '<p>Database settings</p>';
      $form .= '<label for="db_host">Host</label> ';
      $form .= '<input type="text" name="db_host" placeholder="db.example.com" value="' . $_SESSION["db_host"] . '" required><br>';
      $form .= '<label for="db_database">Database name</label> ';
      $form .= '<input type="text" name="db_database" placeholder="database" value="' . $_SESSION["db_database"] . '" required><br>';
      $form .= '<label for="db_username">Username</label> ';
      $form .= '<input type="username" name="db_username" placeholder="root" value="' . $_SESSION["db_username"] . '" required><br>';
      $form .= '<label for="db_password">Password</label> ';
      $form .= '<input type="password" name="db_password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" value="' . $_SESSION["db_password"] . '" required><br>';
    $form .= '<input type="submit">';
  $form .= '</form>';
  echo $form;
}
if ($step == 1) {
  // Prints overview of inserted data for check.
  echo "<div>";
    echo "<h3>Config</h3>";
    echo "<p>GitHub API Token: ";
    echo $_SESSION["api_token"];
    echo "</p><p>Host: ";
    echo $_SESSION["db_host"];
    echo "</p><p>Database name: ";
    echo $_SESSION["db_database"];
    echo "</p><p>Username: ";
    echo $_SESSION["db_username"];
    echo "</p><p>Password: ";
    echo $_SESSION["db_password"];
    echo "</p>";
    echo "<a href='?step=0'>Edit</a> ";
    echo "<a href='?step=2'>Continue</a>";
    if (file_exists("./config.php")) echo "<br><strong>Warning: Config file detected! Following step will rewrite it!</strong>";
  echo "</div>";
}
if ($step == 2) {
  // Creates config file, sets up permissions for files, creates database
  if (file_exists("./config.php")) {
    rename("./config.php", "./config.php_bak".time());
  }
  $f = fopen("./config.php", w);
  $fc = "<?php\n";
  $fc .= "\$api_token = \"" . $_SESSION["api_token"] . "\";\n";
  // $fc .= '$db_address="' . $_SESSION["db_host"] . '";';
  // $fc .= '$db_name="' . $_SESSION["db_database"] . '";';
  // $fc .= '$db_user="' . $_SESSION["db_username"] . '";';
  // $fc .= '$db_pass="' . $_SESSION["db_password"] . '";';
  $fc .= "\$options = [\n";
    $fc .= "\"driver\"   => \"mysqli\",\n";
    $fc .= "\"host\"     => \"" . $_SESSION["db_host"] . "\",\n";
    $fc .= "\"username\" => \"" . $_SESSION["db_username"] . "\",\n";
    $fc .= "\"password\" => \"" . $_SESSION["db_password"] . "\",\n";
    $fc .= "\"database\" => \"" . $_SESSION["db_database"] . "\",\n";
  $fc .= "];\n";
  $fc .= "?>\n";
  fwrite($f, $fc);
  fclose($f);
}



echo "</body>
</html>";
