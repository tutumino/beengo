<?php

require_once('../config/config.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();
// var_dump($_SESSION['event_id']);

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
    $res = $checkLogin->checkMasterPass($_POST['master_pass']);
    $checkLogin->close();
    // var_dump($errMsg);
    if ($res == true) {
        $_SESSION['master_pass'] = $_POST['master_pass'];
        // $_SESSION['address'] = $_POST['address'];
        $_SESSION['master_login'] = 'ok';
        header('Location: ' . SITE_URL . 'master.php?address=' . $_SESSION['address']);
    }
}

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

<?php include ('header.php'); ?>

<div id="input_pass_box" class="shadow2">

    <p>このイベントは<br />パスワードで保護されています</p>
    <p><span>パスワードを入力してログインしてください</span></p>

    <form action="<?php echo SITE_URL . 'master_login.php?address=' . $_SESSION['address'] ?>" method="post">

        <div id="input_pass" class="clearfix">
            <input type="password" name="master_pass" id="" class="input_text" />
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