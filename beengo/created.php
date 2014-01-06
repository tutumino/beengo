<?php

require_once('../config/config.php');
require_once('classes/ManageDB.php');
require_once('classes/ManageEvent.php');

session_start();
// var_dump($_SESSION['eventId']);

$manageEvent = new ManageEvent($_SESSION['event_id']);
$res = $manageEvent->getEvent();
// var_dump($res);
$res = $res->fetch(PDO::FETCH_ASSOC);
// var_dump($res);
// $_SESSION['login'] = '';
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>

    <script>

$(function() {
    $('#event_url').select();
    $('#event_url').focus(function() {
        $(this).select();
    })
    .click(function() {
        $(this).select();
    });
})

    </script>

</head>

<body>

<?php include ('fb.php'); ?>

<?php include ('header.php'); ?>

<div id="created_msg">
    <p>イベントページが作成されました！</p>
    <p><span>イベント名：<?php echo $res['title'] ?></span></p>
<?php
    if ($res['pass'] != '') {
        echo '<p><span>パスワード：' . $res['pass'] . '</span></p>';
    }
?>
</div><!--<created_msg>-->

<div class="this_is_the_URL">
    <p><span>イベントページのURLはこちら</span></p>
    <input type="text" name="" id="event_url" class="input_text" readonly="readonly" value="<?php echo SITE_URL . 'event.php?address=' . $res['address'] ?>" />
    <p class="input_note">上記のURLをコピーして、参加メンバーに知らせてあげてください。<br />もしくは、下のいずれかの共有方法で送信してあげてください。</p>
</div><!--<this_is_the_URL>-->

<?php
// メール文の作成
$shortenedDesc = mb_substr($res['description'], 0, 120) . '……（詳細は下記URLにて）';
$subject = "「{$res['title']}」日程調整のご案内";
$body = "※このメッセージは、{$res['master_name']}さんより、「日程調整・イベント案内ツールBeengo」(" . SITE_URL . ")を使って送信されています。\n\n■イベント概要\n\n{$shortenedDesc}\n\nイベントページにアクセスして、あなたのご都合をお聞かせください。\n\n■イベントページURL\n" . SITE_URL . "event.php?address={$res['address']}\n\n";
if ($res['pass'] != '') {
    $body .= "■パスワード\n{$res['pass']}\n\n";
}
$encSubject = urlencode($subject);
$encBody = urlencode($body);
?>

<div id="event_shere_way" class="shadow2">

    <div id="shere_way_btn_wrapper" class="clearfix">

        <div id="shere_way_send_mail" class="shadow2">
            <a href="mailto:?subject=<?php echo $encSubject ?>&amp;body=<?php echo $encBody ?>">メールで送信</a>
        </div><!--<shere_way_send_mail>-->

        <div id="shere_way_line">
            <span>
            <script type="text/javascript" src="//media.line.naver.jp/js/line-button.js?v=20131101" ></script>
            <script type="text/javascript">
            new jp.naver.line.media.LineButton({"pc":false,"lang":"ja","type":"a","text":"<?php echo SITE_URL . 'event.php?address=' . $res['address'] . ' パスワード：' . $res['pass'] ?>","withUrl":false});
            </script>
            </span>
        </div><!--<shere_way_line>-->

        <div id="shere_way_facebook">
            <div class="fb-send" data-href="<?php echo 'http://beengo.cc/event.php?address=' . $res['address'] ?>" data-width="50" data-height="50" data-colorscheme="light"></div>
        </div><!--<shere_way_facebook>-->

        <div id="shere_way_google">
            <!-- 共有ボタン を表示したい位置に次のタグを貼り付けてください。 -->
            <div class="g-plus" data-action="share" data-annotation="none" data-href="<?php echo SITE_URL . 'event.php?address=' . $res['address'] ?>"></div>

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
    if ($res['pass'] != '') {
        echo '<p class="input_note_S">※イベントページを開くにはパスワードの入力が必要です。<br />パスワードも忘れずに知らせてあげてください。</p>';
    }
?>


    <div class="this_is_the_URL">
        <p><span>マスターページ（幹事用ページ）はこちら</span></p>
        <?php echo '<p id="master_url"><a href="master.php?address=' . $res['address'] . '">' . SITE_URL . 'master.php?address=' . $res['address'] . '</a></p>' ?>
    </div><!--<this_is_the_URL>-->

</body>
</html>