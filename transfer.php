<?php

require_once 'Database.php';
require_once 'SimCard.php';
require_once 'BalanceTransfer.php';

$db = new Database('localhost', 'database_name', 'username', 'password');
$pdo = $db->getConnection();

$fromSimId = $_POST['from_simid'];
$toSimId = $_POST['to_simid'];
$amount = (float)$_POST['amount'];
$comment = $_POST['comment'];

if (empty($fromSimId) || empty($toSimId) || $amount <= 0 || empty($comment)) {
    die("Ошибка: Все поля должны быть заполнены и сумма должна быть положительной.");
}

$fromSim = new SimCard($pdo, $fromSimId);
$toSim = new SimCard($pdo, $toSimId);

$transfer = new BalanceTransfer($pdo, $fromSim, $toSim, $amount, $comment);
$transfer->execute();
