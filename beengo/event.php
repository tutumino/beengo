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

if ($event['pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkLogin();
}

if ($event['flag_fixed'] == 0) {
    $datetimes = $manageEvent->getDatetimes();
    $memberIds = $manageEvent->getMemberIDs();
    $registers = array();
    foreach ($memberIds as $value) {
        $registers[] = $manageEvent->getRegistered($value);
    }
}

if ($event['flag_fixed'] == 1) {
    $fixed = $manageEvent->getFixed();
}

// DB接続解除
$manageEvent->close();

// var_dump($registers);

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
<?php
    if ($event['flag_fixed'] == 1) {
        echo '<script type="text/javascript"
            src="http://maps.google.com/maps/api/js?sensor=false&language=ja"></script>';
        echo '<script type="text/javascript" src="js/googlemaps.js"></script>';
    }
 ?>

 <script>

$(function() {

    $('#answers_table_box').hide();

    $('#answers_table_box_sw').click(function() {
        if ($('#answers_table_box').css('display') == 'none') {
            $('#answers_table_box').fadeIn(500);
            $(this).text('ほかの参加メンバーの登録状況を隠す');
        } else {
            $('#answers_table_box').fadeOut(500);
            $(this).text('ほかの参加メンバーの登録状況を見る');
        }
    });

})

 </script>

</head>

<body <?php if ($event['flag_fixed'] == 1) {echo 'onLoad=mapInit();searchMap();';} ?>>

<?php include_once("analyticstracking.php") ?>

<noscript>
    <META HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.php">
</noscript>

<?php include ('header.php'); ?>

<?php if ($event['flag_fixed'] == 0): ?>

    <div id="invitation_outer">
        <div id="invitation">

            <div class="ribbon">
                <p>Invitation</p>
            </div>

            <div id="master_name_area">
                <p><span><?php echo h($event['master_name']) ?>さん</span>からの、日程調整のご案内です。<br />あなたのご都合を送信してください。</p>
            </div>

            <div id="title_area">
                <h2><?php echo h($event['title']) ?></h2>
            </div>

            <?php if ($event['required_time'] != ''): ?>
                <div id="required_time_area">
                    <p>イベント所要時間：<span><?php echo $event['required_time'] ?></span></p>
                </div>
            <?php endif ?>

            <div id="description_area">
                <p><?php echo nl2br(h($event['description'])) ?></p>
            </div>

        </div><!--<invitation>-->

    </div><!--<invitation_outer>-->

    <form action="register.php" method="post">

        <div id="input_answers_area">

            <p class="stress_red">以下の日時候補に対して、あなたのご都合を選択してください。</p>
            <div id="point_of_hidden_note">
                <p><a id="answers_table_box_sw">ほかの参加メンバーの登録状況を見る</a></p>
            </div><!-- #point_of_hidden_note -->

            <div id="answers_table_box">

                <?php if (empty($registers)): ?>

                    <p>まだ誰も登録していないようです。</p>

                <?php else: ?>

                    <table id="answers_table">
                        <thead></thead>
                        <tbody>
                            <tr class="h_row">
                                <th class="datetimes_col"></th>
                                <?php foreach ($registers as $value):  ?>
                                    <th class="answers_col"><span><?php echo h($value['member_name']) . '</span><br />さん' ?></th>
                                <?php endforeach ?>
                            </tr>
                        <?php for ($i = 0; $i < count($datetimes); $i++): ?>
                            <tr class="row">
                                <td class="datetime_td">
                                    <?php echo '<span class="year">' . $datetimes[$i]['year'] . '年</span>' . $datetimes[$i]['month'] . '月' . $datetimes[$i]['date'] . '日' . $datetimes[$i]['day'] . '<br />' . $datetimes[$i]['time'] ?>
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

                <?php endif; ?>

            </div><!-- #answers_table_box -->

            <?php for ($i = 0; $i < count($datetimes); $i++): ?>

                <div class="input_answer_box">

                    <div class="datetime_box">
                        <?php echo '<p><span class="year">' . $datetimes[$i]['year'] . '年</span>' . $datetimes[$i]['month'] . '月' . $datetimes[$i]['date'] . '日' . $datetimes[$i]['day'] . '<br />' . $datetimes[$i]['time'] . '</p>' ?>
                    </div><!--<datetime_box>-->

                    <div class="datetime_id_box">
                        <?php echo '<input type="hidden" name="datetime_id[' . $i .']" value="' . $datetimes[$i]['datetime_id'] . '" />' ?>
                    </div><!--<datetime_id_box>-->

                    <div class="answer_radio_box">
                        <?php echo '<input type="radio" name="answer[' . $i .']" id="answer_ok' . $i . '" value="ok" /><br />
                        <label for="answer_ok' . $i . '">参加<br />できます！</label>' ?>
                    </div><!--<answer_ok_box>-->

                    <div class="answer_radio_box">
                        <?php echo '<input type="radio" name="answer[' . $i .']" id="answer_maybe' . $i . '" value="maybe" /><br />
                        <label for="answer_maybe' . $i . '">参加<br />できるかも？</label>' ?>
                    </div><!--<answer_maybe_box>-->

                    <div class="answer_radio_box">
                        <?php echo '<input type="radio" name="answer[' . $i .']" id="answer_no' . $i . '" value="no" checked="checked" /><br />
                        <label for="answer_no' . $i . '">参加<br />できません</label>' ?>
                    </div><!--<answer_no_box>-->

                </div><!--<input_answer_box>-->

            <?php endfor; ?>

        </div><!--<input_answers_area>-->

        <div id="input_area">

            <p class="input_title">あなたのお名前（必須）</p>
            <input type="text" name="member_name" id="member_name" class="required count16" value="" placeholder="" />
            <p class="stress_s">※本名を入力する必要はありませんが、<?php echo $event['master_name'] ?>さんがあなただとわかるお名前を入力してあげてください。</p>

            <p class="input_title">備考、<?php echo h($event['master_name']) ?>さんへのメッセージなど</p>
            <textarea name="comment" id="comment" class="count512" rows="5" cols="40" placeholder=""></textarea>

        </div><!--<input_area>-->

        <p class="note_c14">入力内容をよくご確認のうえ、「送信」ボタンで送信してください。<br />（「確認画面」は表示されません。）</p>
        <input type="submit" value="送信" class="btn_red" />

        <!-- 二重ポスト、CSRF対策 -->
        <input type="hidden" name="token" value="<?php echo $token ?>" />

    </form>

<?php elseif ($event['flag_fixed'] == 1): ?>

    <div id="invitation_outer">
        <div id="invitation">

            <div class="ribbon">
                <p>Invitation</p>
            </div>

            <div id="master_name_area">
                <p><span><?php echo h($event['master_name']) ?>さん</span>より、<br />イベント日時決定のご案内です。</p>
            </div>

            <div id="title_area">
                <h2><?php echo h($event['title']) ?></h2>
            </div>

            <div id="fixed_datetime_area">
                <p>決定日時：<br /><?php echo $fixed['year'] . '年<span>' . $fixed['month'] . '月' . $fixed['date'] . '日' . $fixed['day'] . '&nbsp;' . $fixed['time'] . '</span>' ?></p>
                <?php if ($event['required_time'] != ''): ?>
                    <p>所要時間：<?php echo $event['required_time'] ?></p>
                <?php endif ?>

            </div>

            <div id="description_area">
                <p><?php echo nl2br(h($event['description2'])) ?></p>
            </div>

            <?php if ($event['map_type'] != ''): ?>

                <p class="map_type"><?php echo $event['map_type'] ?>地図</p>
                <input type="hidden" name="map_location" id="map_location" class="input_text" value="<?php echo h($event['map_location']) ?>" onLoad="searchMap();" />

                <div id="googlemaps"></div>

            <?php endif ?>

        </div><!--<invitation>-->

    </div><!--<invitation_outer>-->

<?php endif ?>

<?php include 'footer.php' ?>

</body>
</html>