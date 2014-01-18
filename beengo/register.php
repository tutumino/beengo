<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
// require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();
// var_dump($_SESSION['eventId']);
// var_dump($_GET['address']);

// var_dump($eventId);


$checkLogin = new CheckLogin($_SESSION['event_id']);
$checkLogin->checkLogin();

$manageEvent = new ManageEvent($_SESSION['event_id']);

$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// var_dump($event);

if ($event['pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkLogin();
}

$_SESSION['member_id'] = $manageEvent->register($_POST['member_name'], $_POST['comment'], $_POST['datetime_id'], $_POST['answer']);

// DB接続解除
$manageEvent->close();

header('Location: ' . SITE_URL . 'complete?address=' . $_SESSION['address'] . '&memberId=' . $_SESSION['member_id']);
