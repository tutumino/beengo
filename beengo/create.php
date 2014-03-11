<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/RandUnqStr.php');
require_once('classes/CreateEvent.php');
require_once('classes/SendMail.php');

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
$mail = $_POST['mail'];
$datetime = $_POST['datetime'];

$createEvent = new CreateEvent();

$_SESSION['event_id'] = $createEvent->insertEvent($title, $master_name, $description, $required_time, $address, $pass, $mail, $datetime);

// DB接続解除
$createEvent->close();

// 控えのメール送信
if (!empty($_POST['mail'])) {

    $subject = '[Beengo]イベントが作成されました';
    $fromName = 'Beengo';

    $body = "イベント名：$title\r\n";
    $body .= "\r\n";
    $body .= "イベントページURL：\r\n";
    $body .= SITE_URL . 'event.php?address=' . $address . "\r\n";
    $body .= "\r\n";
    $body .= "マスターページURL：\r\n";
    $body .= SITE_URL . 'master.php?address=' . $address . "\r\n";
    $body .= "\r\n";
    if (!empty($pass)) {
        $body .= "パスワード：\r\n";
        $body .= $pass . "\r\n";
        $body .= "\r\n";
    }
    $body .= "-- \r\n";
    $body .= "Beengo | 日程調整・イベント案内ツール\r\n";
    $body .= "http://beengo.cc";

    $sendMail = new SendMail();
    $sendMail->setTo($mail);
    $sendMail->setSubject($subject);
    $sendMail->setFrom(MAIL_FROM);
    $sendMail->setFromName($fromName);
    $sendMail->setBody($body);

    $_SESSION['mail_res'] = $sendMail->send();

}

header('Location: ' . SITE_URL . 'created.php');