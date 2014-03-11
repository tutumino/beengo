<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
// require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

$manageEvent = new ManageEvent($_SESSION['event_id']);

$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// var_dump($event);

if ($event['pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkLogin();
}

$registered = $manageEvent->getRegistered($_SESSION['member_id']);

// DB接続解除
$manageEvent->close();

// var_dump($res['member']);
// var_dump($res['datetime']);
// var_dump($res['answer']);
// var_dump($res);

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1,maximum-scale=1" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>Beengo | 日程調整・イベント案内ツール</title>
    <link rel="shortcut icon" href="http://beengo.cc/favicon.ico" />
    <link rel="apple-touch-icon" href="icon.png" />
    <link href="less/style.less" media="screen and (min-width: 641px)" rel="stylesheet/less" />
    <link href="less/smart.less" media="screen and (max-width: 640px)" rel="stylesheet/less" />
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/less-1.6.1.min.js"></script>
</head>
<body>

<?php include_once("analyticstracking.php") ?>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.php">
</noscript>

<?php include ('fb.php'); ?>

<?php include ('header.php'); ?>

<div id="complete_msg">
    <h2>あなたの入力内容が送信されました。</h2>
    <p><?php echo h($event['master_name']) ?>さんからの連絡をお待ちください。</p>
    <p>ご利用、ありがとうございました。</p>
</div><!--<complete_msg>-->

<p><a href="<?php echo SITE_URL ?>">Beengoのトップページへ</a></p>

<div id="shere_msg">
    <p>もしもBeengoを「役に立った！」と思われたら、<br />「いいね！」「シェア」していただけると、とても嬉しいです。</p>
    <?php include ('sns_btn.php'); ?>
</div><!--<shere_msg>-->

<?php include 'footer.php' ?>

</body>
</html>
