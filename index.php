<?php

require_once __DIR__ . '/functions.php';

if (!isGuest()) {
    header('Location: index.php');
}
$errors = [];

if (!empty($_GET['login']) && !empty($_GET['password'])) {
//    echo $_GET['login'] . ' ' . $_GET['password'] . '<br>';
    if (login($_GET['login'], $_GET['password'])) {
        header('Location: admin.php');
        die;
    }
    else {
        $errors[] = 'Неверный логин или пароль';
    }
}
elseif (!empty($_GET['username'])) {
    logAsGuest($_GET['username']);
    header('Location: list.php');
    die;
}
else {
    $errors[] = 'Представьтесь или зайдите как администратор';
}
?>

<!doctype html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная страница</title>
  </head>

  <body>
    <h1>Авторизация</h1>
    <ul>
      <?php foreach ($errors as $error): ?>
          <li><?= $error ?></li>
      <?php endforeach; ?>
    </ul>
    <p>Чтобы начать работу с тестами надо представиться или зайти как администратор</p>
    <form method="GET">
      <label>Введите ваше имя: </label>
      <input type="text" name="username" value="" placeholder="Ваше имя">
      <button type="submit">Продолжить</button>
    </form>

    <br>
    <hr>
    <form method="GET">
        <label>Введите логин: </label>
        <input type="text" name="login" value="" placeholder="Логин">
        <label>Введите пароль: </label>
        <input type="password" name="password" value="" placeholder="Пароль">
        <button type="submit">Администрирование</button>
    </form>

    </body>
</html>