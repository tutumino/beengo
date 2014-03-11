<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
// require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');
require_once('classes/SendMail.php');

// 二重ポスト、CSRF対策
if (empty($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    die('不正なアクセスです。');
}
unset($_POST['token']);
unset($_SESSION['token']);

$checkLogin = new CheckLogin($_SESSION['event_id']);
$checkLogin->checkLogin();

$manageEvent = new ManageEvent($_SESSION['event_id']);

$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// print_r($event);

if ($event['pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkLogin();
}

$_SESSION['member_id'] = $manageEvent->register($_POST['member_name'], $_POST['comment'], $_POST['datetime_id'], $_POST['answer']);

// DB接続解除
$manageEvent->close();

// 幹事へメール送信
if (!($event['mail'] == null || $event['mail'] == 'deleted')) {

    $subject = "[Beengo]イベント「{$event['title']}」が更新されました";
    $fromName = 'Beengo';

    $body = "{$_POST['member_name']}さんが、「{$event['title']}」の日時候補に対する都合を登録しました。\r\n";
    $body .= "マスターページにて確認してください。\r\n";
    $body .= "\r\n";
    if (!empty($_POST['comment'])) {
        $body .= "{$_POST['member_name']}さんからのコメント：\r\n";
        $body .= "\r\n";
        $body .= "{$_POST['comment']}\r\n";
        $body .= "\r\n";
    }
    $body .= "マスターページURL：\r\n";
    $body .= SITE_URL . 'master.php?address=' . $event['address'] . "\r\n";
    $body .= "\r\n";
    if (!empty($event['pass'])) {
        $body .= "パスワード：\r\n";
        $body .= $event['pass'] . "\r\n";
        $body .= "\r\n";
    }
    $body .= "-- \r\n";
    $body .= "Beengo | 日程調整・イベント案内ツール\r\n";
    $body .= "http://beengo.cc";

    $sendMail = new SendMail();
    $sendMail->setTo($event['mail']);
    $sendMail->setSubject($subject);
    $sendMail->setFrom(MAIL_FROM);
    $sendMail->setFromName($fromName);
    $sendMail->setBody($body);

    $_SESSION['mail_res'] = $sendMail->send();
}


header('Location: ' . SITE_URL . 'complete.php?address=' . $_SESSION['address'] . '&memberId=' . $_SESSION['member_id']);
