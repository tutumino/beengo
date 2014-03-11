<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/ManageEvent.php');

$manageEvent = new ManageEvent($_SESSION['event_id']);
$res = $manageEvent->getEvent();
// var_dump($res);
$res = $res->fetch(PDO::FETCH_ASSOC);
// var_dump($res);
// $_SESSION['login'] = '';

// メール文の作成
$subject = "「{$res['title']}」日程調整のご案内";
$body = "イベントページにアクセスして、あなたのご都合をお聞かせください。\n\n○イベントページURL\n" . SITE_URL . "event.php?address={$res['address']}\n\n";
if ($res['pass'] != '') {
    $body .= "○パスワード\n{$res['pass']}\n\n";
}
$encSubject = urlencode($subject);
$encBody = urlencode($body);
$encSubject_win = urlencode(mb_convert_encoding($subject, 'SJIS', 'UTF-8')); // Windows用（文字化け対策）
$encBody_win = urlencode(mb_convert_encoding($body, 'SJIS', 'UTF-8')); // Windows用（文字化け対策）

if ($_SESSION['mail_res'] === false) {
    echo 'メールの送信に失敗しました。';
}
unset($_SESSION['mail_res']);

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
    $('#event_url').click(function() {
        $(this).select();
    });
    $('#master_url').click(function() {
        $(this).select();
    });
})

$(function() {

    $('.hidden_note').hide();

    $('#hidden_note_sw01').click(function() {
        if ($('#hidden_note01').css('display') == 'none') {
            $('#hidden_note02').fadeOut(100);
            $('#hidden_note_sw02').text('説明を表示');
            $('#hidden_note01').fadeIn(200);
            $(this).text('説明を隠す');
        } else {
            $('#hidden_note01').fadeOut(100);
            $(this).text('説明を表示');
        }
    });

    $('#hidden_note_sw02').click(function() {
        if ($('#hidden_note02').css('display') == 'none') {
            $('#hidden_note01').fadeOut(100);
            $('#hidden_note_sw01').text('説明を表示');
            $('#hidden_note02').fadeIn(200);
            $(this).text('説明を隠す');
        } else {
            $('#hidden_note02').fadeOut(100);
            $(this).text('説明を表示');
        }
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
        <h2>イベントが作成されました！</h2>
        <p>イベント名：<?php echo h($res['title']) ?></p>
    <?php
        if ($res['pass'] != '') {
            echo '<p>パスワード：' . h($res['pass']) . '</p>';
        }
    ?>
    </div><!--<created_msg>-->

    <p class="stress_red">ページを移動する前に、イベントページURL、マスターページURL<?php if ($res['pass'] != '') {echo '、パスワード';} ?>を必ず控えてください。</p>
    <p class="stress_s">※このページを閉じると、URLの確認が二度とできなくなります。また、このページをブックマークすることはできません。</p>

    <div class="point_of_hidden_note">
        <p class="url_title">イベントページURL<a id="hidden_note_sw01">説明を表示</a></p>
        <div class="hidden_note" id="hidden_note01">
            <p>「イベントページ」とは、参加メンバーが、各日時候補に対して、それぞれの都合（「参加できます！」「参加できるかも」「参加できません」の3択）を登録するためのページです。登録が完了すれば、それが「マスターページ」に反映されます。</p>
        </div>
    </div><!-- .point_of_hidden_note -->

    <input type="text" name="" id="event_url" readonly="readonly" value="<?php echo SITE_URL . 'event.php?address=' . $res['address'] ?>" />
    <p id="event_url_s"><?php echo SITE_URL . 'event.php?address=' . $res['address'] ?></p>
    <p class="stress">上記のURLをコピーして、参加メンバーに知らせてあげてください。もしくは、下のいずれかの共有方法で送信してあげてください。</p>

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
                new jp.naver.line.media.LineButton({"pc":false,"lang":"ja","type":"a","text":"<?php echo '○イベントページ：' . SITE_URL . 'event.php?address=' . $res['address']; if ($res['pass'] != '') {echo '  ○パスワード：' . $res['pass'];} ?>","withUrl":false});
                </script>
                </span>
            </div><!--<shere_way_line>-->

            <div id="shere_way_facebook">
                <div class="fb-send" data-href="<?php echo 'http://beengo.cc/event.php?address=' . $res['address'] ?>" data-width="280" data-height="280" data-colorscheme="light"></div>
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
            echo '<p class="stress_s_red">※イベントページを開くにはパスワードの入力が必要です。パスワードも忘れずに知らせてあげてください。</p>';
        }
    ?>

    <div class="point_of_hidden_note">
        <p class="url_title">マスターページURL<a id="hidden_note_sw02">説明を表示</a></p>
        <div class="hidden_note" id="hidden_note02">
            <p>「マスターページ」とは、「イベントページ」にて各参加メンバーが登録した、各日時候補に対しての都合（「参加できます！」「参加できるかも」「参加できません」の3択）を確認するための幹事専用ページです。</p>
        </div>
    </div><!-- .point_of_hidden_note -->

    <input type="text" name="" id="master_url" readonly="readonly" value="<?php echo SITE_URL . 'master.php?address=' . $res['address'] ?>" />
    <p id="master_url_s"><?php echo SITE_URL . 'master.php?address=' . $res['address'] ?></p>

</div><!--<created_wrapper>-->

<?php include 'footer.php' ?>

</body>
</html>