<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

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

    // DB接続解除
    $checkLogin->close();
    
    // var_dump($errMsg);
    if ($res == true) {
        $_SESSION['pass'] = $_POST['pass'];
        // $_SESSION['address'] = $_POST['address'];
        $_SESSION['login'] = 1;
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
    <link href="less/style.less" media="screen and (min-width: 641px)" rel="stylesheet/less" />
    <link href="less/smart.less" media="screen and (max-width: 640px)" rel="stylesheet/less" />
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/less-1.6.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.ah-placeholder.js"></script>
</head>

<body>

<?php include_once("analyticstracking.php") ?>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.php">
</noscript>

<?php include ('header.php'); ?>

<div id="input_pass_box">

    <p>このイベントは<br />パスワードで保護されています</p>
    
    <form action="<?php echo SITE_URL . 'login.php?address=' . $_SESSION['address'] ?>" method="post">

        <div id="input_pass">
            <input type="password" name="pass" id="" placeholder="パスワード" />
            <input type="submit" value="ログイン" />
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