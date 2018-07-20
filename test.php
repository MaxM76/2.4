<?php

require_once __DIR__ . '/functions.php';


if (!isGuest() and !isAdmin()) {
    header('Location: index.php');
    die;
}

function to_integer(&$item1)
{
    $item1 = (integer) $item1;
}

function makeRecursive($array = "")
{
    $toflat = array($array);
    $result = array();
    while (($r = array_shift($toflat)) !== NULL)
        foreach ($r as $v)
            if (is_array($v)) {
                $toflat[] = $v;
            }
            else {
                $result[] = $v;
            }
    return $result;
}



if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}
else {
    $action = 'none';
}

switch ($action) {
    case 'test':
        if (!empty($_GET['test_nm'])) {
            $testNmb = $_GET['test_nm'];
        }

        $jsonfileList = glob("*.json");
        if (($jsonfileList === false) or (count($jsonfileList) == 0)) {
            if (isAdmin()) {
                echo '<a href="admin.php">Перейти к форме загрузки тестов</a><br>';
                echo '<a href="list.php">Перейти к форме выбора теста</a><br>';
                echo '<a href="logout.php">Выйти из режима администрирования</a><br>';
            }
            else {
                echo '<a href="list.php">Перейти к форме выбора теста</a><br>';
                echo '<a href="index.php">Перейти к режиму администрирования</a><br>';
            }
            exit('Ошибка поиска .json файлов');
        }

        if (($testNmb >= 1) and ($testNmb <= count($jsonfileList))) {
            $testfile = $jsonfileList[$testNmb - 1];
        }
        else {
            header("HTTP/1.0 404 Not Found");
            exit('Тест не найден');
            echo '<a href="list.php">Перейти к форме выбора теста</a>';
            if (!isAdmin()) {
                echo '<a href="index.php">Перейти к режиму администрирования</a><br>';
            }
            else {
                echo '<a href="admin.php">Перейти к форме загрузки тестов</a><br>';
            }
        }

        $testJSON = file_get_contents($testfile);
        $curTest = json_decode($testJSON, true);
        if ($curTest === null) {
            exit('Ошибка декодирования .json файла');
        }
        $maxSum = 0;
        break;
    case 'calc':
        if (!empty($_GET['q'])) {
            $goals = $_GET['q'];
        }
        if (!empty($_GET['max'])) {
            $maxGoals = $_GET['max'];
        }
        if (!empty($_GET['testfile'])) {
            $testfile = $_GET['testfile'];
        }
        else {
            $testfile = '';
        }
        if ($maxGoals == 0) {
            exit('Ошибка подсчета максимально возможного результата');
        }
        array_walk_recursive ($goals , 'to_integer');

        $sumGoals = array_sum(makeRecursive($goals));
        $testResult = $sumGoals / $maxGoals * 100;

        $name = getName();
        putenv('GDFONTPATH=' . realpath('.'));
        $certificateImage = imagecreatetruecolor(1024, 722);
        $textColor = imagecolorallocate($certificateImage, 0, 0, 0);
        $fontFile = 'Xiomara-Script.ttf';
        $imBox = imagecreatefromjpeg('blank.jpg');
        imagecopy($certificateImage, $imBox, 0, 0, 0, 0, 1024, 722);
        imagettftext($certificateImage, 20, 0, 170, 390, $textColor, $fontFile, $name);
        imagettftext($certificateImage, 20, 0, 170, 420, $textColor, $fontFile, 'Вы прошли тест ' . $testfile);
        imagettftext($certificateImage, 20, 0, 170, 450, $textColor, $fontFile, 'С результатом ' . $testResult . '%');
        imagejpeg($certificateImage, 'certificate.jpg');
        imagedestroy($certificateImage);

        echo 'Ваш результат: ' . $testResult . '%<br>';
        exit('Тест пройден');
    default:
        echo '<a href="admin.php">Перейти к форме загрузки тестов</a><br>';
        exit('Ошибка передачи параметра действия');
}

?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Тесты: прохождение теста</title>
  </head>

  <body>
    <h1>Прохождение теста <?= $testfile ?></h1>

    <form action="test.php" method="GET">
      <?php
      $i = 0;
      $j = 0;
      foreach ($curTest as $curQuestion) {
          $j++;
          if (count($curQuestion["answers"]) == count($curQuestion["results"])) {
              $i++;
          }
          else {
              continue;
          }
/*          if ($curQuestion["is_single"] == 'true') {
              $inputType = 'radio';
          }
          else {
              $inputType = 'checkbox';
          }
*/
          $inputType = 'checkbox'
      ?>
        <fieldset>
          <legend>Вопрос № <?= $i ?>: <?=$curQuestion["question"] ?></legend>
          <ol>
          <?php
          $q = 1;
          foreach ($curQuestion["answers"] as $key => $curAnswer) {
          ?>
              <li><input type="<?= $inputType ?>" name="<?= 'q[' . $j .']['. $key . ']'?>" value="<?= $curQuestion["results"][$q] ?>"><?= $curAnswer ?></li>
          <?php
              $maxSum = $maxSum + $curQuestion["results"][$q];
              $q++;
          }
          ?>

          </ol>
        </fieldset>
          <?php
        }
        ?>
      <input type="hidden" name="action" value="calc">
      <input type="hidden" name="max" value="<?= $maxSum ?>">
      <input type="hidden" name="testfile" value="<?= $testfile ?>">
      <input type="submit" value="Посчитать результаты">
    </form>

  </body>
</html>
