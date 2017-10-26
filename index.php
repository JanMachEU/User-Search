<?php
session_start();
require "config.php";
require_once "Builder.php";
require_once "./dibi/loader.php";

$build = new Builder();
$db = new DibiConnection($options);

$build->head();
$build->menu();
$build->containerMain();
$build->form($_POST["username"]);

if (isset($_POST["username"]) && $_POST["username"] !== "" && $build->isValid($_POST["username"])) {
  $username = $_POST["username"];
  $query = <<<JSON
  query(\$login:String!) {
    user(login: \$login) {
      login
      name
      bioHTML
      avatarUrl
      repositories(last: 100) {
        nodes {
          name
          description
          updatedAt
          stargazers{totalCount}
        }
      }
    }
    organization(login: \$login) {
      login
      name
      description
      avatarUrl
      repositories(last: 100) {
        nodes {
          name
          description
          updatedAt
          stargazers{totalCount}
        }
      }
    }
  }
JSON;

$variables ="
{
  \"login\": \"".$username."\"
}
";

  $json = json_encode(['query' => $query, 'variables' => $variables]);

  $curl = curl_init();

  curl_setopt($curl, CURLOPT_URL, "https://api.github.com/graphql");
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_USERAGENT, "GUS");
  curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: bearer ".$_SESSION["api_token"]));
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $api_result_raw = curl_exec($curl);

  curl_close($curl);
  $api_result = json_decode($api_result_raw, true);

  if (isset($api_result["errors"]["0"]["type"]) && isset($api_result["errors"]["1"]["type"])) {
    $build->error($api_result);
  } else {
    $build->repo_container($api_result);
    $build->containerMainEnd();
    $build->widgetL($api_result);
  }
  if ($build->isValid($_POST["username"])) {
    $db->query('INSERT INTO history ', [
                  'query' => $username,
                  'ip' => $_SERVER["REMOTE_ADDR"],
                  ]);
  }
}

if (isset($_POST["username"]) && $_POST["username"] !== "" && !$build->isValid($_POST["username"])) {
  echo "<div class=\"container\">";
  echo "Error: Prohibited character!";
  echo "</div>";
}

$build->footer();
?>
