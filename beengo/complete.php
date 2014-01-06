<?php

require_once('../config/config.php');
require_once('classes/ManageDB.php');
// require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();

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
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
</head>
<body>

<?php include ('fb.php'); ?>

<?php include ('header.php'); ?>

<div id="complete_msg">
    <p>あなたの入力内容が送信されました。</p>
    <p><?php echo $event['master_name'] ?>さんからの連絡をお待ちください。</p>
    <p>ご利用、ありがとうございました。</p>
    <p><a href="<?php echo SITE_URL ?>">Beengoのトップページへ</a></p>
</div><!--<complete_msg>-->

<div id="shere_msg">
    <p>もしもBeengoを「役に立った！」と思われたら、<br />「いいね！」「シェア」していただけると、とても嬉しいです！</p>
    <?php include ('sns.php'); ?>
</div><!--<shere_msg>-->
    
</body>
</html>
