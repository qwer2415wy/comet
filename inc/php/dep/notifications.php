<?php
include 'permissions.php';

$user = $_SESSION['user']->getUsername();

$stmnt = $pdo->prepare('SELECT notifications FROM nm_accounts WHERE username=?;');
$stmnt->bindParam(1, $user, PDO::PARAM_STR);
$stmnt->execute();

$notification = $stmnt->fetchAll()[0][0];

$empty = '[]';
$stmnt = $pdo->prepare('UPDATE nm_accounts SET notifications=? WHERE username=?;');
$stmnt->bindParam(1, $empty, PDO::PARAM_STR);
$stmnt->bindParam(2, $user, PDO::PARAM_STR);
$stmnt->execute();

die($notification);