<?php
require "config.php";
include_once './dibi/loader.php';
/**
 *
 */
class Builder
{

  function __construct()
  {

  }

  public function head()
  {
    echo "<!DOCTYPE html>
    <html>
      <head>
        <meta charset=\"utf-8\">
        <title>GitHub User Search - Jan Mach</title>
        <link rel=\"stylesheet\" href=\"./css/master.css\">
        <link rel=\"stylesheet\" href=\"./css/colors.css\">
        <link rel=\"stylesheet\" href=\"./css/grid.css\">
        <link rel=\"stylesheet\" href=\"./css/font-awesome.min.css\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
      </head>\n";
  }

  public function menu()
  {
    echo "<body>
    <div class=\"wrapper\">
    <ul class=\"menu topmenu color-primary-2\">
    <li class=\"color-primary-hover-3\"><a href=\"./index.php\">Home</a></li>
    <li class=\"color-primary-hover-3\"><a href=\"./history.php\">History</a></li>
  </ul>";
  }

  public function containerMain()
  {
    echo "<div class=\"main color-primary-1\">";
  }

  public function containerMainEnd()
  {
    echo "</div>";
  }

  public function form($username)
  {
    echo "<div class=\"container\">";
      echo "<h1>User search</h1>";
      echo "<form method=\"POST\" action=\"./index.php\">";
        echo "<input type=\"text\" name=\"username\" placeholder=\"GitHub username\" value=\"".$username."\"> ";
        echo "<input type=\"submit\">";
    echo "</form>\n</div>";
  }

  public function widgetL($api_result)
  {
    if (isset($api_result["data"]["user"])) {
      echo "<div class=\"leftw color-primary-1\"><div class=\"container\">";
        echo "<img src=\"".$api_result["data"]["user"]["avatarUrl"]."\" alt=\"avatar\" id=\"avatarImg\">";
        echo "<a href=\"https://github.com/".$api_result["data"]["user"]["login"]."\">";
          echo "<h2>".$api_result["data"]["user"]["name"]."</h2>";
        echo "</a><br>";
        echo "<span>".$api_result["data"]["user"]["bioHTML"]."</span>";
      echo "</div>\n</div>";
    } else {
      echo "<div class=\"leftw color-primary-1\"><div class=\"container\">";
        echo "<img src=\"".$api_result["data"]["organization"]["avatarUrl"]."\" alt=\"avatar\" id=\"avatarImg\">";
        echo "<a href=\"https://github.com/".$api_result["data"]["organization"]["login"]."\">";
          echo "<h2>".$api_result["data"]["organization"]["name"]."</h2>";
        echo "</a><br>";
        echo "<span>".$api_result["data"]["organization"]["description"]."</span>";
      echo "</div>\n</div>";
    }

  }

  public function repo_container($api_result)
  {
    $now = new DateTime();

    if (isset($api_result["data"]["user"])) {
      $rows = count($api_result["data"]["user"]["repositories"]["nodes"]);
      echo "<div class=\"container\">";
      for ($i=($rows-1); $i >= 0; $i--) {
        echo "<div>";
          echo "<a href=\"https://github.com/" . $api_result["data"]["user"]["login"] . "/" . $api_result["data"]["user"]["repositories"]["nodes"][$i]["name"] . "\"><h4>" . $api_result["data"]["user"]["repositories"]["nodes"][$i]["name"] . "</h4></a> ";
          echo "<span><i class=\"fa fa-star\" aria-hidden=\"true\"></i>" . $api_result["data"]["user"]["repositories"]["nodes"][$i]["stargazers"]["totalCount"] . "</span>";
          echo "<p>" . $api_result["data"]["user"]["repositories"]["nodes"][$i]["description"] . "</p>";
        echo "</div><hr>";
      }
      echo "</div>";
    } else {
      $rows = count($api_result["data"]["organization"]["repositories"]["nodes"]);
      echo "<div class=\"container\">";
      for ($i=($rows-1); $i >= 0; $i--) {
        echo "<div>";
          echo "<a href=\"https://github.com/" . $api_result["data"]["organization"]["login"] . "/" . $api_result["data"]["organization"]["repositories"]["nodes"][$i]["name"] . "\"><h4>" . $api_result["data"]["organization"]["repositories"]["nodes"][$i]["name"] . "</h4></a> ";
          echo "<span><i class=\"fa fa-star\" aria-hidden=\"true\"></i>" . $api_result["data"]["organization"]["repositories"]["nodes"][$i]["stargazers"]["totalCount"] . "</span>";
          echo "<p>" . $api_result["data"]["organization"]["repositories"]["nodes"][$i]["description"] . "</p>";
        echo "</div><hr>";
      }
      echo "</div>";
    }

  }

  public function history_container($history, $actual = 0, $step = 10)
  {
    $rows = count($history);
    if ($rows == 0) {
      echo "<div class=\"container_history\">\n";
      echo "Nothing in history.\n";
      echo "</div>\n";
    }
    $from = $actual * $step;
    $to = ($actual * $step) + $step;
    if ($to > $rows) $to = $rows;

    $row = $history->fetchAll();
    for ($i=$from; $i < $to; $i++) {
      echo "<div class=\"container_history\">\n";
      echo "<h4>[" . ($i+1) . "] " . $row[$i]["date"]->format("d. m. Y H:i:s") . "\n";
      echo " [" . $row[$i]["ip"] . "] </h4>\n";
      echo "<span>query: " . $row[$i]["query"] . "</span>\n";
      echo "</div>\n";
    }
  }

  public function history_navigator($history, $actual = 0, $step = 10)
  {
    $rows = count($history);
    $pages = ceil($rows/$step);
    if($pages <= 1) return;
    echo "<div class=\"container\">";
    for ($i=0; $i < $pages; $i++) {
      if ($i == $actual) {
        echo "<a href='?p=" . $i . "'><strong>" . ($i + 1) . "</strong></a> \n";
      } else {
        echo "<a href='?p=" . $i . "'>" . ($i + 1) . "</a> \n";
      }
    }
    echo "<a href='history_delete.php'>Delete history</a>";
    echo "</div>";
  }

  public function history_delete_form($deleted=0)
  {
    echo "<div class=\"container\">";
    echo "<form class='history_delete_form' action='history_delete.php' method='post'>";
    echo "Delete older than <input type='number' name='hours' min='0'> hours.";
    echo "<input type='submit' value='Delete'>";
    echo "</form>";
    if ($deleted > 0) {
      echo "<div>";
      echo "Succesfully deleted " . $deleted . " rows from database!";
      echo "</div>";
    }
    echo "</div>";

  }

  public function isValid($str)
  {
    return !preg_match("/[^A-Za-z0-9_-]/", $str);
  }

  public function error($api_result)
  {
    if ($api_result["errors"][0][type] == "NOT_FOUND") {
      echo "<div class=\"container\">";
      echo "Error: User not found!";
      echo "</div>";
    } else {
      echo "<div class=\"container\">";
      echo "Error: ".$api_result["errors"][0][message];
      echo "</div>";
    }
  }

  public function footer()
  {
    echo "</div></body>\n</html>";
  }
}
?>
