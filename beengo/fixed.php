<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');


session_start();

// $_SESSION['address'] = $_GET['address'];
// $getEventId = new GetEventID;
// $_SESSION['event_id'] = $getEventId->get($_SESSION['address']);

$manageEvent = new ManageEvent($_SESSION['event_id']);
$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// var_dump($event);

if ($event['master_pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkMasterLogin();
}

if ($event['flag_fixed'] == 0) {
    header('Location: ' . SITE_URL);
}

$fixed = $manageEvent->getFixed();
// var_dump($fixed);

// DB接続解除
$manageEvent->close();

// メール文の作成
// $shortenedDesc = mb_substr($event['description2'], 0, 120) . '……（詳細は下記URLにて）';
$subject = "「{$event['title']}」日時決定のご案内";
$body = "イベントページにアクセスして、日時とイベントの詳細をご確認ください。\n\n○イベントページURL\n" . SITE_URL . "event?address={$_SESSION['address']}\n\n";
if ($event['pass'] != '') {
    $body .= "○パスワード\n{$event['pass']}\n\n";
}
$encSubject = urlencode($subject);
$encBody = urlencode($body);

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/import.css">
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>

    <script>

$(function() {
    $('#event_url').select();
    $('#event_url').click(function() {
        $(this).select();
    });
})

    </script>

</head>

<body>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript">
</noscript>

<?php include ('fb.php'); ?>

<?php include ('header.php'); ?>

<div id="created_wrapper" class="shadow2">

    <div id="created_msg">
        <p><span>「<?php echo h($event['title']) ?>」</span>のイベントページが更新されました！</p>
        <p>日時は<span><?php echo $fixed['year'] . '年' . $fixed['month'] . '月' . $fixed['date'] . '日' . $fixed['day'] . $fixed['time'] ?></span>に決定しました。</p>
    </div><!--<fixed_msg>-->

    <div class="this_is_the_URL">
        <p><span>イベントページのURLはこちら</span></p>
        <input type="text" name="" id="event_url" class="input_text" readonly="readonly" value="<?php echo SITE_URL . 'event?address=' . $_SESSION['address'] ?>" />
        <p id="event_url_s"><?php echo SITE_URL . 'event?address=' . $_SESSION['address'] ?></p>
        <p class="input_note">上記のURLをコピーして、参加メンバーに知らせてあげてください。<br />もしくは、下のいずれかの共有方法で送信してあげてください。</p>
    </div><!--<this_is_the_URL>-->

    <div id="event_shere_way">

        <div id="shere_way_btn_wrapper" class="clearfix">

            <div id="shere_way_send_mail" class="shadow2">
                <a href="mailto:?subject=<?php echo $encSubject ?>&amp;body=<?php echo $encBody ?>">メールで送信</a>
            </div><!--<shere_way_send_mail>-->

            <div id="shere_way_line">
                <span>
                <script type="text/javascript" src="//media.line.naver.jp/js/line-button.js?v=20131101" ></script>
                <script type="text/javascript">
                new jp.naver.line.media.LineButton({"pc":false,"lang":"ja","type":"a","text":"<?php echo SITE_URL . 'event?address=' . $_SESSION['address'] . ' パスワード：' . $event['pass'] ?>","withUrl":false});
                </script>
                </span>
            </div><!--<shere_way_line>-->

            <div id="shere_way_facebook">
                <div class="fb-send" data-href="<?php echo 'http://beengo.cc/event?address=' . $_SESSION['address'] ?>" data-width="50" data-height="50" data-colorscheme="light"></div>
            </div><!--<shere_way_facebook>-->

            <div id="shere_way_google">
                <!-- 共有ボタン を表示したい位置に次のタグを貼り付けてください。 -->
                <div class="g-plus" data-action="share" data-annotation="none" data-href="<?php echo SITE_URL . 'event?address=' . $_SESSION['address'] ?>"></div>

                <!-- 最後の 共有 タグの後に次のタグを貼り付けてください。 -->
                <script type="text/javascript">
                  window.___gcfg = {lang: 'ja'};

                  (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/platform.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>
            </div><!--<shere_way_google>-->

        </div><!--<shere_way_btn_wrapper>-->

    </div><!--<event_shere_way>-->
    <?php
        if ($event['pass'] != '') {
            echo '<p class="input_note_S">※イベントページを開くにはパスワードの入力が必要です。<br />パスワードも忘れずに知らせてあげてください。</p>';
        }
    ?>

</div><!--<created_wrapper>-->

<div id="shere_msg">
    <p>もしもBeengoを「役に立った！」と思ったら、<br />「いいね！」「シェア」していただけると、とても嬉しいです！</p>
    <?php include ('sns.php'); ?>
</div><!--<shere_msg>-->


</body>
</html>