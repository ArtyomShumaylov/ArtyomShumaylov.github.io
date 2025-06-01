<?php
function connect_project_db() {
    $user = 'u68534';
    $pass = '9542530';
    $dsn = 'mysql:host=localhost;dbname=u68534;charset=utf8';
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
