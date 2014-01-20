<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();
// var_dump($_SESSION['event_id']);
// var_dump($_SESSION['address']);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // $getEventId = new GetEventID();
    // var_dump($getEventId);
    // $eventId = $getEventId->get($_GET['address']);
    // var_dump($eventId);
    // $getEventId->close();
    // var_dump($getEventId);

    // $address = $_GET['address'];
    $res = '';
    
}

// var_dump($_SERVER['REQUEST_METHOD']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $checkLogin = new CheckLogin($_SESSION['event_id']);
    // var_dump($checkLogin);
    $res = $checkLogin->checkPass($_POST['pass']);
    $checkLogin->close();
    // var_dump($errMsg);
    if ($res == true) {
        $_SESSION['pass'] = $_POST['pass'];
        // $_SESSION['address'] = $_POST['address'];
        $_SESSION['login'] = 'ok';
        header('Location: ' . SITE_URL . 'event.php?address=' . $_SESSION['address']);
    }
}

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
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/import.css">
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.ah-placeholder.js"></script>
</head>

<body>

<?php include_once("analyticstracking.php") ?>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.php">
</noscript>

<?php include ('header.php'); ?>

<div id="input_pass_box" class="shadow2">

    <p>このイベントは<br />パスワードで保護されています</p>
    <p><span>パスワードを入力して<br />ログインしてください</span></p>
    
    <form action="<?php echo SITE_URL . 'login.php?address=' . $_SESSION['address'] ?>" method="post">

        <div id="input_pass" class="clearfix">
            <input type="password" name="pass" id="" class="input_text" placeholder="パスワード" />
            <input type="submit" value="ログイン" class="btn_red" />
        </div><!--<input_pass>-->

    </form>

</div><!--<input_pass_area>-->

<?php
    if ($res === false) {
        echo '<p class="err_msg">パスワードが正しくありません。</p>';
    }
?>

</body>
</html>