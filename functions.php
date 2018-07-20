<?php
session_start();

function getUsers() : array
{
    $fileData = file_get_contents(__DIR__ . '/data/admins.json');
    $users = json_decode($fileData, true);
    if (!$users) {
        return [];
    }
    return $users;
}


function getUser($login) : ?array
{
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['login'] == $login) {
            return $user;
        }
    }
    return null;
}


function login(string $login, string $password) : bool
{
    $user = getUser($login);
    if ($user && $user['password'] == $password) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}


function logAsGuest(string $username)
{
    $_SESSION['guestname'] = $username;
}


function isAdmin()
{
    return !empty($_SESSION['user']) && !empty($_SESSION['user']['is_admin']);
}


function isGuest() {
    return !empty($_SESSION['guestname']);
}

function getName() : string
{
    if (isAdmin()) {
        return $_SESSION['user']['username'];
    }
    elseif (isGuest()) {
        return $_SESSION['guestname'];
    }
    else {
        return '';
    }
}
