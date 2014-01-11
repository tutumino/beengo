<?php

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();

$_SESSION['address'] = $_GET['address'];
$getEventId = new GetEventID;
$_SESSION['event_id'] = $getEventId->get($_SESSION['address']);

$manageEvent = new ManageEvent($_SESSION['event_id']);

$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// var_dump($event);

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
// var_dump($datetimes);
// var_dump($memberIds);
// var_dump($registers[0]);
// var_dump($event);

// DB接続解除
$manageEvent->close();

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
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

    </script>

</head>

<!-- <body onload="mapInit();"> -->
<body>

<?php include ('header.php'); ?>

<form action="fix.php" method="post">

    <div id="answers_table_area">
        <table id="answers_table">
            <thead></thead>
            <tbody>
                <tr class="h_row">
                    <th class="radio_col"></th>
                    <th class="datetimes_col"></th>
                    <?php foreach ($registers as $value):  ?>
                        <th class="answers_col"><?php echo $value['member_name'] . '<br />さん' ?></th>
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
                            if ($temp['ok'] == count($registers))  {
                                echo '<td class="level1 answer_td">';
                             // すべて「noでない」なら、tdのclassにlevel2を付ける
                            } elseif ($temp['no'] == 0) {
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

            <div class="comment_wrapper" class="clearfix">

                <div class="member_name clearfix">
                    <p>
                        <?php echo $registers[$i]['member_name'] . '<br />さん' ?>
                    </p>
                </div><!--<member_name>-->

                <div class="comment_outer clearfix">
                    <div class="comment">
                        <?php echo nl2br($registers[$i]['comment']) ?>
                    </div><!--<comment>-->
                </div><!--<comment_outer>-->

            </div><!--<comment_wrapper>-->

        <?php endfor ?>

    </div><!--<comments>-->

    <p id="create_invitation_btn"><a>ラジオボタンで選択した日時で<br />「イベントページ」を日時決定案内状に更新する▼</a></p>

    <div id="input_invitation">

        <div class="input_part">
            <p class="input_note">イベント概要、コメントなどを入力してください。<br />変更を加えなければ、「イベントページ」作成時の文面が<br />そのまま挿入されます。</p>
            <textarea name="description2" id="description2" cols="40" rows="10" class="textarea count1280" placeholder="イベントの概要、詳細、コメントなど"><?php echo $event['description']; ?></textarea>
        </div>

        <div class="input_part" id="select_map_type">
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
        </div>

        <div id="search_map_btn" class="clearfix">
            <input type="text" name="map_location" id="map_location" class="input_text count64" placeholder="住所、駅名、建物名などを入力して、表示させたい地図を検索" />
            <input type="button" value="検索" class="btn_red" onclick="searchMap();" />
        </div><!--<search_map_btn>-->

        <div id="googlemaps"></div>

        <div class="input_part">
            <p class="input_note">入力内容をよくご確認のうえ、<br />「イベントページを更新」ボタンを押してください。<br />（確認ページは表示されません）</p>
            <input type="submit" class="btn_red shadow" value="イベントページを更新" />
        </div><!--<input_part>-->

    </div><!--<input_invitation>-->

</form>
</body>
</html>