<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>Beengoについて | Beengo | 日程調整・イベント案内ツール</title>
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1,maximum-scale=1" />
    <meta name="description" content="">
    <link rel="shortcut icon" href="http://beengo.cc/favicon.ico" />
    <link rel="apple-touch-icon" href="icon.png" />
    <link href="less/style.less" media="screen and (min-width: 641px)" rel="stylesheet/less" />
    <link href="less/smart.less" media="screen and (max-width: 640px)" rel="stylesheet/less" />
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/less-1.6.1.min.js"></script>
    <script type="text/javascript" src="js/smooth_scroll.js"></script>
</head>

<body>

<?php include_once("analyticstracking.php") ?>

<?php include 'header.php' ?>

<div id="user_agreement">

    <div class="formal">

        <h2>Beengoについて</h2>

        <p>Beengoはただいま、ベータ版としてリリースされています（2014/1/20現在）。</p>
        <p>時期をみて正式版をリリースいたしますが、現在もご自由に使用していただけます。</p>
        <p>また、ベータ版にて作成したデータは、正式版リリース後も引き続きご利用いただけます。</p>
        <p>運営団体：特定非営利活動法人サインポスト</p>

        

    </div><!--<formal>-->

</div><!--<user_agreement>-->

<?php include 'footer.php' ?>

</body>
</html>