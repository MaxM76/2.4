<?php

require_once __DIR__ . '/functions.php';

if (!isGuest() and !isAdmin()) {
    header('Location: index.php');
    die;
}


$jsonfileList = glob("*.json");
if (($jsonfileList === false) or (count($jsonfileList) == 0)) {
    echo '<a href="admin.php">Перейти к форме загрузки тестов</a><br>';
    exit('Ошибка поиска .json файлов');
}

$maxTestIndex = count($jsonfileList);

if (!empty($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isAdmin()) {
        if (!empty($_GET['test_nm'])) {
            $testNmb = $_GET['test_nm'];
            if (($testNmb >= 1) and ($testNmb <= count($jsonfileList))) {
                $deletingFile = $jsonfileList[$testNmb - 1];
                unlink($deletingFile);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Тесты: Список тестов</title>
  </head>

  <body>
    <p>Привет, <?= getName() ?></p><br/>
    <h1>Список тестов</h1>
    <ol>
        <?php
        foreach ($jsonfileList as $item) {
            echo "<li>" . $item . "</li>";
        }
        ?>
    </ol>


    <form action=<?= (isAdmin()) ? "list.php" : "test.php" ?> method="GET">
      <p>Введите номер теста, который Вы хотите <?= (isAdmin()) ? 'удалить' : 'пройти'?></p>
      <input type="number" name="test_nm" value="" min="1" max="<?= $maxTestIndex?>">
      <input type="hidden" name="action" value=<?= (isAdmin()) ? "delete" : "test" ?>>
      <input type="submit" value=<?= (isAdmin()) ? "Удалить тест" : "Пройти тест" ?>>
    </form>
    <?php
    if (isAdmin()) {
        echo '<a href="admin.php">Перейти к форме загрузки тестов</a><br>';
        echo '<a href="logout.php">Выйти из режима администрирования</a><br>';
    }
    ?>
    <form method="GET">

    </form>
  </body>
</html>