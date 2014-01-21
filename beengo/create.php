<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/RandUnqStr.php');
require_once('classes/CreateEvent.php');

session_start();

// 二重ポスト、CSRF対策
if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    die('不正なアクセスです。');
}
unset($_POST['token']);
unset($_SESSION['token']);

$address = new RandUnqStr();
$address = $address->getStr();

$title = $_POST['title'];
$master_name = $_POST['master_name'];
$description = $_POST['description'];
$required_time = $_POST['required_time'];
$pass = $_POST['pass'];
$datetime = $_POST['datetime'];

$createEvent = new CreateEvent();

$_SESSION['event_id'] = $createEvent->insertEvent($title, $master_name, $description, $required_time, $address, $pass, $datetime);

// DB接続解除
$createEvent->close();

header('Location: ' . SITE_URL . 'created.php');