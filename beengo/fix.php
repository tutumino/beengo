<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();

// 二重ポスト、CSRF対策
if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    die('不正なアクセスです。');
}
unset($_POST['token']);
unset($_SESSION['token']);

$manageEvent = new ManageEvent($_SESSION['event_id']);
$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// var_dump($event);

if ($event['master_pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkMasterLogin();
}

$fix = $_POST['fix'];
$description2 = $_POST['description2'];
switch ($_POST['map_type']) {
    case 'map_type1':
        $map_type = '会場';
        break;
    case 'map_type2':
        $map_type = '待ち合わせ場所';
        break;
    default:
        $map_type = '';
        break;
}
if (!($_POST['map_location'] == '')) {
    $map_location = $_POST['map_location'];
} else {
    $map_location = '';
}

$manageEvent = new ManageEvent($_SESSION['event_id']);
$manageEvent->fix($fix, $description2, $map_type, $map_location);


header('Location: ' . SITE_URL . 'fixed.php?address=' . $_SESSION['address']);