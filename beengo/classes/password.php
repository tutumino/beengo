<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once('../config/config.php');
    require_once('classes/ManageDB.php');
    require_once('classes/GetEventID.php');
    require_once('classes/CheckLogin.php');
    // require_once('classes/ManageEvent.php');

    $eventId = new GetEventID();
    $eventId = $eventId->get($_GET['address']);

    $checklogin = new CheckLogin();
    $checklogin->checkPass($eventId, $_POST['pass']);

}

// パスワード（半角英数16文字以内）（必須）
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    
    <form action="" method="post">
        <input type="password" name="pass" id="" />
        <input type="submit" value="ログイン">
    </form>

</body>
</html>