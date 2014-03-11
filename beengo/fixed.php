<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

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
$subject = "「{$event['title']}」日時決定のご案内";
$body = "イベントページにアクセスして、日時とイベントの詳細をご確認ください。\n\n○イベントページURL\n" . SITE_URL . "event.php?address={$_SESSION['address']}\n\n";
if ($event['pass'] != '') {
    $body .= "○パスワード\n{$event['pass']}\n\n";
}
$encSubject = urlencode($subject);
$encBody = urlencode($body);
$encSubject_win = urlencode(mb_convert_encoding($subject, 'SJIS', 'UTF-8')); // Windows用（文字化け対策）
$encBody_win = urlencode(mb_convert_encoding($body, 'SJIS', 'UTF-8')); // Windows用（文字化け対策）

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

<?php include_once("analyticstracking.php") ?>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.php">
</noscript>

<?php include ('fb.php'); ?>

<?php include ('header.php'); ?>

<div id="created_wrapper">

    <div id="created_msg">
        <h2>「<?php echo h($event['title']) ?>」のイベントページが更新されました！</h2>
        <p>日時は<?php echo $fixed['year'] . '年' . $fixed['month'] . '月' . $fixed['date'] . '日' . $fixed['day'] . $fixed['time'] ?>に決定しました。</p>
    </div><!--<created_msg>-->

    <div class="this_is_the_URL">
        <p class="url_title">イベントページURL</p>
        <input type="text" name="" id="event_url" readonly="readonly" value="<?php echo SITE_URL . 'event.php?address=' . $_SESSION['address'] ?>" />
        <p id="event_url_s"><?php echo SITE_URL . 'event.php?address=' . $_SESSION['address'] ?></p>
        <p class="stress">上記のURLをコピーして、参加メンバーに知らせてあげてください。もしくは、下のいずれかの共有方法で送信してあげてください。</p>
    </div><!--<this_is_the_URL>-->

    <div id="event_shere_way">

        <div id="shere_way_btn_wrapper">

            <div id="shere_way_send_mail">
                <!-- Windowsの場合とそれ意外の場合で分岐（文字化け防止対策） -->
                <?php $ua = $_SERVER['HTTP_USER_AGENT']; ?>
                <?php if (stripos($ua, 'windows') != false): ?>
                    <a href="mailto:?subject=<?php echo $encSubject_win ?>&amp;body=<?php echo $encBody_win ?>">メールで送信</a>
                <?php else: ?>
                    <a href="mailto:?subject=<?php echo $encSubject ?>&amp;body=<?php echo $encBody ?>">メールで送信</a>
                <?php endif; ?>
            </div><!--<shere_way_send_mail>-->

            <div id="shere_way_line">
                <span>
                <script type="text/javascript" src="//media.line.naver.jp/js/line-button.js?v=20131101" ></script>
                <script type="text/javascript">
                new jp.naver.line.media.LineButton({"pc":false,"lang":"ja","type":"a","text":"<?php echo '○イベントページ：' . SITE_URL . 'event.php?address=' . $_SESSION['address']; if ($event['pass'] != '') {echo '  ○パスワード：' . $event['pass'];} ?>","withUrl":false});
                </script>
                </span>
            </div><!--<shere_way_line>-->

            <div id="shere_way_facebook">
                <div class="fb-send" data-href="<?php echo 'http://beengo.cc/event.php?address=' . $_SESSION['address'] ?>" data-width="50" data-height="50" data-colorscheme="light"></div>
            </div><!--<shere_way_facebook>-->

            <div id="shere_way_google">
                <!-- 共有ボタン を表示したい位置に次のタグを貼り付けてください。 -->
                <div class="g-plus" data-action="share" data-annotation="none" data-href="<?php echo SITE_URL . 'event.php?address=' . $_SESSION['address'] ?>"></div>

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
            echo '<p class="stress_s_red">※イベントページを開くにはパスワードの入力が必要です。パスワードも忘れずに知らせてあげてください。</p>';
        }
    ?>

</div><!--<created_wrapper>-->

<div id="shere_msg">
    <p>ご利用ありがとうございました。</p>
    <p>もしもBeengoを「役に立った！」と思われたら、<br />「いいね！」「シェア」していただけると、とても嬉しいです。</p>
    <?php include ('sns_btn.php'); ?>
</div><!--<shere_msg>-->

<?php include 'footer.php' ?>

</body>
</html>