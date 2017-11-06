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
    $_SESSION["db_password"] = $_POST["db_password"];
}
if ($step == 0) {
  // Default state. Creates form
  echo "<h1>Instalation</h1>";
    echo "<ul>";
    echo "<li>Fill the form</li>";
    echo "<li>Check the data</li>";
    echo "<li>Config file will be created and permissions will be setted</li>";
    echo "<li>Database table will be created</li>";
  echo "</ul>";
  $form = '<form class="" action="install.php?step=1" method="post">';
    $form .= '<p>API Settings</p>';
      $form .= '<label for="api_token">GitHub API Token</label> ';
      $form .= '<input type="text" name="api_token" value="' . $_SESSION["api_token"] . '" required>';
    $form .= '<p>Database settings</p>';
      $form .= '<label for="db_host">Host</label> ';
      $form .= '<input type="text" name="db_host" placeholder="db.example.com" value="' . $_SESSION["db_host"] . '" required><br>';
      $form .= '<label for="db_username">Username</label> ';
      $form .= '<input type="username" name="db_username" placeholder="root" value="' . $_SESSION["db_username"] . '" required><br>';
      $form .= '<label for="db_password">Password</label> ';
      $form .= '<input type="password" name="db_password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" value="' . $_SESSION["db_password"] . '"><br>';
      $form .= '<label for="db_database">Database name</label> ';
      $form .= '<input type="text" name="db_database" placeholder="database" value="' . $_SESSION["db_database"] . '" required><br>';
    $form .= '<input type="submit">';
  $form .= '</form>';
  echo $form;
}
if ($step == 1) {
  // Prints overview of inserted data for check.
  echo "<h1>Step 1 / 3</h1>";
  echo "<div>";
    echo "<h3>Please check this</h3>";
    echo "<p>GitHub API Token: ";
    echo $_SESSION["api_token"];
    echo "</p><p>Host: ";
    echo $_SESSION["db_host"];
    echo "</p><p>Database name: ";
    echo $_SESSION["db_database"];
    echo "</p><p>Username: ";
    echo $_SESSION["db_username"];
    echo "</p><p>Password: ";
    if ($_SESSION["db_password"] == "") echo "-NOT SET-";
    else echo $_SESSION["db_password"];
    echo "</p>";
    echo "<a href='?step=0'>Edit</a> ";
    echo "<a href='?step=2'>Continue</a>";
    if (file_exists("./config.php")) echo "<br><strong>Warning: Config file detected! Following step will rewrite it!</strong>";
  echo "</div>";
}
if ($step == 2) { // Creates config file, sets up permissions for files
  echo "<h1>Step 2 / 3</h1>";
  if (!isset($_SESSION["api_token"]) || $_SESSION["api_token"] == "") {
    die("ERROR: Please set API token. <a href='?step=0'>Edit</a>");
  }
  if (!isset($_SESSION["db_host"]) || $_SESSION["db_host"] == "") {
    die("ERROR: Please set Database Host. <a href='?step=0'>Edit</a>");
  }
  if (!isset($_SESSION["db_username"]) || $_SESSION["db_username"] == "") {
    die("ERROR: Please set Database Username. <a href='?step=0'>Edit</a>");
  }
  // if (!isset($_SESSION["db_password"]) || $_SESSION["db_password"] == "") {
  //   echo("WARNING: Database Password is not set. ");
  // }
  if (!isset($_SESSION["db_database"]) || $_SESSION["db_database"] == "") {
    die("ERROR: Please set Database name. <a href='?step=0'>Edit</a>");
  }
  if (file_exists("./config.php")) {
    $new_name = "./config.php_bak".time();
    rename("./config.php", $new_name);
    echo "<p>Old config file was renamed to: " . $new_name . "</p>";
  }

  $f = fopen("./config.php", "w");
  $fc = "<?php\n";
  $fc .= "\$api_token = \"" . $_SESSION["api_token"] . "\";\n";
  $fc .= "\$options = [\n";
    $fc .= "\"driver\"   => \"mysqli\",\n";
    $fc .= "\"host\"     => \"" . $_SESSION["db_host"] . "\",\n";
    $fc .= "\"username\" => \"" . $_SESSION["db_username"] . "\",\n";
    if ($_SESSION["db_password"] !== "") $fc .= "\"password\" => \"" . $_SESSION["db_password"] . "\",\n";
    $fc .= "\"database\" => \"" . $_SESSION["db_database"] . "\",\n";
  $fc .= "];\n";
  $fc .= "?>\n";
  $created = fwrite($f, $fc);
  fclose($f);
  if ($created != FALSE) {
    echo "<p>Config file succesfully created.</p><p>You can now <a href=\"?step=3\">CONTINUE</a>.</p>";
  }else {
    echo "<p>ERROR: Unable to create config file!</p>";
  }

  // Changing file permissions
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("."));
  echo "Setting up permissions.<br>\n";
  chmod("./css", 0755);
  chmod("./dibi", 0755);
  chmod("./fonts", 0755);
  foreach($iterator as $file) {
      if (substr($file, -1) != ".") {
        if (substr($file, -4) == ".php") {
          chmod($file, 0640);
          // echo "Permissions for <strong>'" . $file . "'</strong> set to <strong>" . decoct(fileperms($file) & 0777) . "</strong>\n<br>";
        }
        if (substr($file, -4) == ".css" || substr($file, -4) == ".min") {
          chmod($file, 0755);
          // echo "Permissions for <strong>'" . $file . "'</strong> set to <strong>" . decoct(fileperms($file) & 0777) . "</strong>\n<br>";
        }
        if (substr($file, -4) == ".otf" || substr($file, -4) == ".eot" || substr($file, -4) == ".svg") {
          chmod($file, 0755);
          // echo "Permissions for <strong>'" . $file . "'</strong> set to <strong>" . decoct(fileperms($file) & 0777) . "</strong>\n<br>";
        }
        if (substr($file, -4) == ".ttf" || substr($file, -5) == ".woff" || substr($file, -6) == ".woff2") {
          chmod($file, 0755);
          // echo "Permissions for <strong>'" . $file . "'</strong> set to <strong>" . decoct(fileperms($file) & 0777) . "</strong>\n<br>";
        }
      }
  }
}
if ($step == 3) {
  // Creates database
  echo "<h1>Step 3 / 3</h1>";
  require "./config.php";
  $db = new DibiConnection($options);
  $result = $db->query("CREATE TABLE IF NOT EXISTS `history` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `query` text COLLATE utf8_czech_ci NOT NULL,
    `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ip` text COLLATE utf8_czech_ci NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=0 ;
");
  // print_r($result);
  // if ($result == TRUE) {
    echo "<p>Database table succesfully created. You can now enjoy <a href=\"./index.php\">searching people on GitHub</a></p>";
  // }
  // else {
  //   echo "<h3>ERROR</h3>";
  //   echo "<strong>Database table could NOT be created. Please check database credentials: </strong>\n<br>";
  //   echo "</p><p>Host: ";
  //   echo $_SESSION["db_host"];
  //   echo "</p><p>Database name: ";
  //   echo $_SESSION["db_database"];
  //   echo "</p><p>Username: ";
  //   echo $_SESSION["db_username"];
  //   echo "</p><p>Password: ";
  //   if ($_SESSION["db_password"] == "") echo "-NOT SET-";
  //   else echo $_SESSION["db_password"];
  //   echo "</p>";
  //   echo "<strong>Please check if you have permissions to create a table in database.</strong><br>";
  //   echo "<strong>Please check that you don't have table with name \"history\" in your database.</strong>";
  // }
}


echo "</body>
</html>";
