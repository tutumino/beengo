<?php

require_once('../config/config.php');
require_once('classes/ManageDB.php');
require_once('classes/GetEventID.php');
require_once('classes/CheckLogin.php');
require_once('classes/ManageEvent.php');

session_start();
// var_dump($_SESSION['eventId']);
// var_dump($_GET['address']);

// $eventId = new GetEventID();
// $_SESSION['eventId'] = $eventId->get($_GET['address']);

// var_dump($eventId);

$_SESSION['address'] = $_GET['address'];
$getEventId = new GetEventID;
$_SESSION['event_id'] = $getEventId->get($_SESSION['address']);
// var_dump($_SESSION['event_id']);

    


$manageEvent = new ManageEvent($_SESSION['event_id']);
$event = $manageEvent->getEvent();
$event = $event->fetch(PDO::FETCH_ASSOC);
// var_dump($event);

if ($event['pass'] != '') {
    $checkLogin = new CheckLogin($_SESSION['event_id']);
    $checkLogin->checkLogin();
}

if ($event['flag_fixed'] == 0) {
    $datetimes = $manageEvent->getDatetimes();
}

if ($event['flag_fixed'] == 1) {
    $fixed = $manageEvent->getFixed();
}

// var_dump($datetimes);

// DB接続解除
$manageEvent->close();
// var_dump($_SESSION['address'])

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/input_sample.js"></script>
    <script type="text/javascript" src="js/validate_event.js"></script>
<?php 
    if ($event['flag_fixed'] == 1) {
        echo '<script type="text/javascript"
            src="http://maps.google.com/maps/api/js?sensor=false&language=ja"></script>';
        echo '<script type="text/javascript" src="js/googlemaps.js"></script>';
    }
 ?>

    <script>

// 「あなたのお名前」サンプルテキスト
var menberNameSample = 'あなたのお名前（必須）';
inputSample('#member_name', menberNameSample);

// 「コメント」サンプルテキスト
var commentSample = '備考、<?php echo $event['master_name'] ?>さんへのメッセージなど';
inputSample('#comment', commentSample);

    </script>

</head>

<body <?php if ($event['flag_fixed'] == 1) {echo 'onLoad=mapInit();searchMap();';} ?>>

<?php include ('header.php'); ?>

<?php if ($event['flag_fixed'] == 0): ?>

    <div id="invitation_outer">
        <div id="invitation" class="shadow2">

            <div class="ribbon shadow">
                <p>Invitation</p>
            </div>


            <div id="master_name_area">
                <p><span><?php echo $event['master_name'] ?>さん</span>からの、日程調整のご案内です。<br />あなたのご都合を送信してください。</p>
            </div>           

            <div id="title_area">
                <h2><?php echo $event['title'] ?></h2>
            </div> 

            <div id="description_area">
                <p><?php echo nl2br($event['description']) ?></p>
            </div>

        </div><!--<invitation>-->
        
    </div><!--<invitation_outer>-->

    <?php if ($event['required_time'] != ''): ?>
        <div id="required_time_area">
            <p>イベント所要時間：<span><?php echo $event['required_time'] ?></span></p>
        </div>
    <?php endif ?>

    <p class="input_note_L">以下の日時候補に対して、あなたのご都合を選択してください。</p>

    <form action="register.php" method="post">

        <div id="input_answers_area">
        
            <?php for ($i = 0; $i < count($datetimes); $i++): ?>

                <div class="input_answer_box clearfix">

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

        <div class="input_part">
            <input type="text" name="member_name" id="member_name" class="input_text required" value="" />
            <p class="input_note">本名フルネームを入力する必要はありませんが、<br /><?php echo $event['master_name'] ?>さんがあなただとわかるお名前を入力してあげてください。</p>
        </div>

        <div class="input_part">
            <textarea name="comment" id="comment" class="textarea" rows="5" cols="40"></textarea>
        </div>
        
        <div class="input_part">
            <p class="input_note">入力内容をよくご確認のうえ、「送信」ボタンで送信してください。<br />「確認画面」は表示されません。</p>
            <input type="submit" value="送信" class="btn_red shadow" />
        </div>
    </form>

<?php elseif ($event['flag_fixed'] == 1): ?>

    <div id="invitation_outer">
        <div id="invitation" class="shadow2">

            <div class="ribbon shadow">
                <p>Invitation</p>
            </div>

            <div id="master_name_area">
                <p><span><?php echo $event['master_name'] ?>さん</span>より、<br />イベント日時決定のご案内です。</p>
            </div>           

            <div id="title_area">
                <h2><?php echo $event['title'] ?></h2>
            </div>

            <div id="fixed_datetime_area">
                <p>決定日時：<?php echo $fixed['year'] . '年<span>' . $fixed['month'] . '月' . $fixed['date'] . '日' . $fixed['day'] . '&nbsp;' . $fixed['time'] . '</span>' ?></p>
                <?php if ($event['required_time'] != ''): ?>
                    <p>所要時間：<?php echo $event['required_time'] ?></p>
                <?php endif ?>

            </div>

            <div id="description_area">
                <p><?php echo nl2br($event['description']) ?></p>
            </div>

            <p class="map_type"><?php echo $event['map_type'] ?>地図</p>
            <input type="hidden" name="map_location" id="map_location" class="input_text" value="<?php echo $event['map_location'] ?>" onLoad="searchMap();" />

            <div id="googlemaps"></div>

        </div><!--<invitation>-->
        
    </div><!--<invitation_outer>-->

<?php endif ?>
        
</body>
</html>