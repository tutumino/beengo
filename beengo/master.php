<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

// 二重ポスト、CSRF対策
$token = sha1(uniqid(rand(), true));
$_SESSION['token'] = $token;

$_SESSION['address'] = $_GET['address'];
$getEventId = new GetEventID;
$_SESSION['event_id'] = $getEventId->get($_SESSION['address']);

$manageEvent = new ManageEvent($_SESSION['event_id']);

$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);

if ($event['flag_fixed'] == 1) {
    header('Location: ' . SITE_URL . 'fixed.php?address=' . $_SESSION['address']);
}

if ($event['master_pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkMasterLogin();
}

$datetimes = $manageEvent->getDatetimes();
$memberIds = $manageEvent->getMemberIDs();
$registers = array();
foreach ($memberIds as $value) {
    $registers[] = $manageEvent->getRegistered($value);
}

// DB接続解除
$manageEvent->close();

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
    <script type="text/javascript" src="js/validate.js"></script>
    <script type="text/javascript" src="js/prevent_enter_submit.js"></script>
    <script type="text/javascript" src="js/jquery.ah-placeholder.js"></script>
    <script type="text/javascript"
            src="http://maps.google.com/maps/api/js?sensor=false&language=ja"></script>
    <script type="text/javascript" src="js/googlemaps.js"></script>

    <script>

$(function() {
    $('#input_invitation').hide();
    $('#create_invitation_btn').on('click', function() {
        var target = $(this).offset().top;
        $('#input_invitation').slideDown(500);
        mapInit();
        $('body').animate({ scrollTop: target }, 700);
    });
})

$(function() {

    $('.hidden_note').hide();

    $('#hidden_note_sw01').click(function() {
        if ($('#hidden_note01').css('display') == 'none') {
            $('#hidden_note01').fadeIn(200);
            $(this).text('説明を隠す');
        } else {
            $('#hidden_note01').fadeOut(100);
            $(this).text('説明を表示');
        }
    });

})

    </script>

</head>

<!-- <body onload="mapInit();"> -->
<body>

<?php include_once("analyticstracking.php") ?>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.php">
</noscript>

<?php include ('header.php'); ?>

<form action="fix.php" method="post">

    <p class="master_page_top_note">参加者がイベントページで、各日時候補に対しての都合を登録すると、下の表に反映されます。</p>

    <div id="answers_table_area">

        <table id="answers_table">
            <thead></thead>
            <tbody>
                <tr class="h_row">
                    <th class="radio_col"></th>
                    <th class="datetimes_col"></th>
                    <?php foreach ($registers as $value):  ?>
                        <th class="answers_col"><span><?php echo h($value['member_name']) . '</span><br />さん' ?></th>
                    <?php endforeach ?>
                </tr>
            <?php for ($i = 0; $i < count($datetimes); $i++): ?>
                <tr class="row">
                    <td class="radio_td">
                        <input type="radio" name="fix" id="fix<?php echo $datetimes[$i]['datetime_id'] ?>" value="<?php echo $datetimes[$i]['datetime_id'] ?>" />
                    </td>
                    <td class="datetime_td">
                        <label for="fix<?php echo $datetimes[$i]['datetime_id'] ?>">
                            <?php echo '<span class="year">' . $datetimes[$i]['year'] . '年</span>' . $datetimes[$i]['month'] . '月' . $datetimes[$i]['date'] . '日' . $datetimes[$i]['day'] . '<br />' . $datetimes[$i]['time'] ?>
                        </label>
                    </td>
                    <?php
                        // ok, maybe, no それぞれの数を調べる
                        for ($j = 0; $j < count($registers); $j++) {
                            $temp[] = $registers[$j]['answer'][$i];
                        }
                        if (isset($temp)) {
                            $temp = array_count_values($temp);
                        }

                        for ($j = 0; $j < count($registers); $j++) {
                            // すべて「ok」なら、tdのclassにlevel1を付ける
                            if (isset($temp['ok']) && $temp['ok'] == count($registers))  {
                                echo '<td class="level1 answer_td">';
                             // 「no」が1つもなければ、tdのclassにlevel2を付ける
                            } elseif (!isset($temp['no'])) {
                                echo '<td class="level2 answer_td">';
                            // それ以外なら、tdのclassにlevelを付けない
                            } else {
                                echo '<td class="answer_td">';
                            }
                            switch ($registers[$j]['answer'][$i]) {
                                case 'ok':
                                    echo '<img src="img/answer_ok.png" alt="" width="50" height="50" />';
                                    break;
                                case 'maybe':
                                    echo '<img src="img/answer_maybe.png" alt="" width="50" height="50" />';
                                    break;
                                case 'no':
                                    echo '<img src="img/answer_no.png" alt="" width="50" height="50" />';
                                    break;
                            }
                            echo '</td>';
                        }
                    ?>
                </tr>
            <?php endfor ?>
            </tbody>
        </table>
    </div><!--<answers_table_area>-->


    <div id="comments">

        <?php for ($i = 0; $i < count($registers); $i++): ?>

        <?php if ($registers[$i]['comment'] == '') {continue; } ?>

            <div class="comment_wrapper">

                <div class="member_name">
                    <p>
                        <span><?php echo h($registers[$i]['member_name']) . '</span><br />さん' ?>
                    </p>
                </div><!--<member_name>-->

                <div class="comment_outer clearfix">
                    <div class="comment">
                        <?php echo nl2br(h($registers[$i]['comment'])) ?>
                    </div><!--<comment>-->
                </div><!--<comment_outer>-->

            </div><!--<comment_wrapper>-->

        <?php endfor ?>

    </div><!--<comments>-->

    <p class="note_c14">ラジオボタンで選択した日時でイベント案内状を作成するには<br />「イベント情報を入力」ボタンをクリック！</p><!-- .text_c14 -->

    <input type="button" id="create_invitation_btn" value="イベント情報を入力" />

    <div id="input_invitation">

        <p class="input_title">イベントの詳細、参加者へのコメントなど</p>
        <p class="stress_s">※変更を加えなければ、「イベント」作成時の文面がそのまま挿入されます。</p>
        <textarea name="description2" id="description2" cols="40" rows="10" class="textarea count1280" placeholder=""><?php echo $event['description']; ?></textarea>

        <div id="select_map_type">
            <p>
                <input type="radio" name="map_type" id="map_type1" class="map_type" value="map_type1" checked />
                <label for="map_type1">「会場」として地図を付ける</label>
            </p>
            <p>
                <input type="radio" name="map_type" id="map_type2" class="map_type" value="map_type2" />
                <label for="map_type2">「待ち合わせ場所」として地図を付ける</label>
            </p>
            <p>
                <input type="radio" name="map_type" id="map_type3" class="map_type" value="map_type3" />
                <label for="map_type3">地図を付けない</label>
            </p>
        </div><!-- #select_map_type -->

        <div class="point_of_hidden_note">
            <p class="input_title">地図を検索<a id="hidden_note_sw01">説明を表示</a></p>
            <div class="hidden_note" id="hidden_note01">
                <p>「案内状」に表示させたい地図を検索します。目的の地図が検索されたキーワードを、必ずそのまま残しておいてください。</p>
                <p>地名、駅名、建物名などで目的の場所が検索されない場合は、住所での検索をお試しください。（検索の精度は「Google マップ」の仕様によります）。</p>
            </div>
        </div><!-- .point_of_hidden_note -->
        <div id="search_map_btn">
            <input type="text" name="map_location" id="map_location" class="count64" placeholder="住所、地名、駅名、建物名など" />
            <input type="button" value="検索" onclick="searchMap();" />
        </div><!--<search_map_btn>-->

        <div id="googlemaps"></div>

        <p class="note_c14">入力内容をよくご確認のうえ、<br />「イベントページを更新」ボタンを押してください。<br />（確認ページは表示されません）</p>
        <input type="submit" class="btn_red shadow" value="イベント案内状を作成" />

    </div><!--<input_invitation>-->

    <!-- 二重ポスト、CSRF対策 -->
    <input type="hidden" name="token" value="<?php echo $token ?>" />

</form>

<?php include 'footer.php' ?>

</body>
</html>