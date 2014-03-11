<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $res = '';
}

// var_dump($_SERVER['REQUEST_METHOD']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $checkLogin = new CheckLogin($_SESSION['event_id']);
    // var_dump($checkLogin);
    $res = $checkLogin->checkMasterPass($_POST['master_pass']);

    // DB接続解除
    $checkLogin->close();
    // var_dump($errMsg);
    if ($res == true) {
        $_SESSION['master_pass'] = $_POST['master_pass'];
        // $_SESSION['address'] = $_POST['address'];
        $_SESSION['master_login'] = 1;
        header('Location: ' . SITE_URL . 'master.php?address=' . $_SESSION['address']);
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

<div id="input_pass_box" class="shadow2">

    <p>このイベントは<br />パスワードで保護されています</p>

    <form action="<?php echo SITE_URL . 'master_login.php?address=' . $_SESSION['address'] ?>" method="post">

        <div id="input_pass">
            <input type="password" name="master_pass" id="" placeholder="パスワード" />
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