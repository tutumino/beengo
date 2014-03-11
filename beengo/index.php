<?php
session_start();

require_once('../config/config.php');
require_once('../config/jp_setting.php');
require_once('funcs/funcs.php');

// 二重ポスト、CSRF対策
$token = sha1(uniqid(rand(), true));
$_SESSION['token'] = $token;

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1,maximum-scale=1" />
    <title>Beengo | 日程調整・イベント案内ツール</title>
    <meta name="description" content="日程調整、イベント案内が、とても簡単にできるWebアプリケーション。たくさんの日時候補も専用カレンダーでラクラク入力。日程調整が完了すれば、地図付き（Googleマップ）案内状のできあがり。" />
    <meta name="keywords" content="日程調整,スケジュール調整,イベント案内,幹事,会合,飲み会,会議,ミーティング,新年会,忘年会,パーティー" />
    <link rel="shortcut icon" href="http://beengo.cc/favicon.ico" />
    <link rel="apple-touch-icon" href="icon.png" />
    <link href="less/style.less" media="screen and (min-width: 641px)" rel="stylesheet/less" />
    <link href="less/smart.less" media="screen and (max-width: 640px)" rel="stylesheet/less" />
    <script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
    <script type="text/javascript" src="js/less-1.6.1.min.js"></script>
    <script type="text/javascript" src="js/cal.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    <script type="text/javascript" src="js/prevent_enter_submit.js"></script>
    <script type="text/javascript" src="js/smooth_scroll.js"></script>
    <script type="text/javascript" src="js/jquery.ah-placeholder.js"></script>
    <script src='js/jquery.customSelect.min.js'></script>

    <script>

// 追加された日時をマウスオーバーでハイライト表示
$(function () {
    $(document).on('mouseover', '#added_datetime_table tr', function() {
        $(this).css('background-color', '#f7ebe8');
    });
    $(document).on('mouseout', '#added_datetime_table tr', function() {
        $(this).css('background-color', 'transparent');
    });
})

// 時間の指定の有効/無効の動作
$(function() {
    $('input:radio[name="time_OnOff01"], input:radio[name="time_OnOff02"], input:radio[name="time_OnOff03"], input:radio[name="time_OnOff04"]').change(function () {
        if ($('input:radio[name="time_OnOff01"]:checked').val() == 'off') {
            $('#hour01').prop('disabled', true);
            $('#min01').prop('disabled', true);
            $('#time_off02').prop('checked', true);
            $('input:radio[name="time_OnOff02"]').prop('disabled', true);
            $('#time_off03').prop('checked', true);
            $('input:radio[name="time_OnOff03"]').prop('disabled', true);
            $('#time_off04').prop('checked', true);
            $('input:radio[name="time_OnOff04"]').prop('disabled', true);
        } else {
            $('#hour01').prop('disabled', false);
            $('#min01').prop('disabled', false);
            $('input:radio[name="time_OnOff02"]').prop('disabled', false);
        }
        if ($('input:radio[name="time_OnOff02"]:checked').val() == 'off') {
            $('#hour02').prop('disabled', true);
            $('#min02').prop('disabled', true);
            $('#time_off03').prop('checked', true);
            $('input:radio[name="time_OnOff03"]').prop('disabled', true);
            $('#time_off04').prop('checked', true);
            $('input:radio[name="time_OnOff04"]').prop('disabled', true);
        } else {
            $('#hour02').prop('disabled', false);
            $('#min02').prop('disabled', false);
            $('input:radio[name="time_OnOff03"]').prop('disabled', false);
        }
        if ($('input:radio[name="time_OnOff03"]:checked').val() == 'off') {
            $('#hour03').prop('disabled', true);
            $('#min03').prop('disabled', true);
            $('#time_off04').prop('checked', true);
            $('input:radio[name="time_OnOff04"]').prop('disabled', true);
        } else {
            $('#hour03').prop('disabled', false);
            $('#min03').prop('disabled', false);
            $('input:radio[name="time_OnOff04"]').prop('disabled', false);
        }
        if ($('input:radio[name="time_OnOff04"]:checked').val() == 'off') {
            $('#hour04').prop('disabled', true);
            $('#min04').prop('disabled', true);
        } else {
            $('#hour04').prop('disabled', false);
            $('#min04').prop('disabled', false);
        }
    })

})

// キャプチャ画像のスライドショー
var timerID = setInterval(function() {
        $('#captures_wrapper').animate({
            marginLeft : parseInt($('#captures_wrapper').css('margin-left')) - 500 + 'px'
        }, 300, 'swing', function() {
            $('#captures_wrapper').css('margin-left', '-' + 500 + 'px');
                $('#captures_wrapper > img:first').appendTo('#captures_wrapper');
        })
    }, 5000);

$(function() {

    $('.hidden_note').hide();

    $('#hidden_note_sw01').click(function() {
        if ($('#hidden_note01').css('display') == 'none') {
            $('#hidden_note02').fadeOut(100);
            $('#hidden_note_sw02').text('説明を表示');
            $('#hidden_note03').fadeOut(100);
            $('#hidden_note_sw03').text('説明を表示');
            $('#hidden_note04').fadeOut(100);
            $('#hidden_note_sw04').text('説明を表示');
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
            $('#hidden_note03').fadeOut(100);
            $('#hidden_note_sw03').text('説明を表示');
            $('#hidden_note04').fadeOut(100);
            $('#hidden_note_sw04').text('説明を表示');
            $('#hidden_note02').fadeIn(200);
            $(this).text('説明を隠す');
        } else {
            $('#hidden_note02').fadeOut(100);
            $(this).text('説明を表示');
        }
    });

    $('#hidden_note_sw03').click(function() {
        if ($('#hidden_note03').css('display') == 'none') {
            $('#hidden_note01').fadeOut(100);
            $('#hidden_note_sw01').text('説明を表示');
            $('#hidden_note02').fadeOut(100);
            $('#hidden_note_sw02').text('説明を表示');
            $('#hidden_note04').fadeOut(100);
            $('#hidden_note_sw04').text('説明を表示');
            $('#hidden_note03').fadeIn(200);
            $(this).text('説明を隠す');
        } else {
            $('#hidden_note03').fadeOut(100);
            $(this).text('説明を表示');
        }
    });

    $('#hidden_note_sw04').click(function() {
        if ($('#hidden_note04').css('display') == 'none') {
            $('#hidden_note01').fadeOut(100);
            $('#hidden_note_sw01').text('説明を表示');
            $('#hidden_note02').fadeOut(100);
            $('#hidden_note_sw02').text('説明を表示');
            $('#hidden_note03').fadeOut(100);
            $('#hidden_note_sw03').text('説明を表示');
            $('#hidden_note04').fadeIn(200);
            $(this).text('説明を隠す');
        } else {
            $('#hidden_note04').fadeOut(100);
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


<?php  include ('fb.php'); ?>

<?php include ('header.php'); ?>

<div id="top_area">

    <div id="top">

        <div id="catch">

            <h2>忙しい幹事さんへ。<br />日程調整、イベント案内を、<br />もっとスマートに。</h2>

            <p id="immediately"><a href="#create_event_area">今すぐ使ってみる。</a></p>

            <?php include ('sns_btn.php'); ?>

        </div><!--<catch>-->

        <div id="captures_area">
            <div id="captures_wrapper">
                <img src="img/capture01.png" height="300" width="500" alt="" />
                <img src="img/capture02.png" height="300" width="500" alt="" />
                <img src="img/capture03.png" height="300" width="500" alt="" />
                <img src="img/capture04.png" height="300" width="500" alt="" />
            </div><!--<captures_wrapper>-->
        </div><!--<captures_area>-->

        <div id="appeal_boxes">
            <div class="appeal_box">
                <h3>幹事にも<br />参加メンバーにも<br />嬉しいツール。</h3>
                <p>幹事を引き受けて、意外と大変なのが日程調整。Beengo（ビーンゴ）を使えば、日時候補をサクサクと入力して「イベント」を作成し、URLを参加メンバーに教えてあげるだけ。参加者はただ「イベントページ」で参加可能な日時にチェックを入れるだけ。すると、マスターページ（幹事用のページ）で、イベント開催に最適な日時が一目でわかる表が自動的にできあがります。紙にみんなの予定を書き出して悩む必要はありません。</p>
            </div><!--<appeal_box>-->
            <div class="appeal_box">
                <h3>イベント告知、<br />日時決定の案内も<br />スマートに。</h3>
                <p>Beengoを使えば、何人もの参加メンバーとたくさんのメールを交わす必要はありません。イベントに「日時決定」の操作を施せば、「イベントページ」が会場や待ち合わせ場所の地図（Googleマップ）付きの案内状になります。「イベントページ」へのリンクは、Beengo上から自動的にメール本文に挿入させたり、FacebookやGoogle+、LINE（スマホのみ対応）で簡単に共有することもできます。</p>
            </div><!--<appeal_box>-->
            <div class="appeal_box">
                <h3>手間いらずで安心。<br />誰でも<br />今すぐに使えます。</h3>
                <p>ユーザー登録、アカウント作成などは不要です。あなたの個人情報を永続的にお預かりすることもありません。</p>
                <p>「イベントページ」と「マスターページ」には、誰でも見ることができないようにパスワードを設定することもできます。また、作成したイベントはイベント開催日から一ヶ月で自動的に消去されます。</p>
                <p>使い方も簡単。説明に従って入力、操作していけば、数ステップで完了します。お気軽に試してみてください。</p>

            </div><!--<appeal_box>-->
        </div><!--<appeal_boxes>-->

    </div><!--<top>-->

</div><!--<top_area>-->

<div id="create_event_area">

    <div id="create_event_h">
        <h3>イベントを作成する。</h3>
        <p>必要事項を入力し、カレンダーを使って日時候補を追加し、最下部の「イベントを作成」ボタンを押してください。</p>
    </div><!--<create_event_h>-->

    <form action="create.php" method="post">

        <div id="input_event_info">

            <p class="input_title">イベント名（必須）</p>
            <input type="text" name="title" id="title" class="required count64" value="" placeholder="" />

            <p class="input_title">イベント所要時間</p>
            <select name="required_time" id="required_time">
                <option value="">指定なし</option>
                <option value="30分">30分</option>
                <option value="1時間">1時間</option>
                <option value="1時間30分">1時間30分</option>
                <option value="2時間">2時間</option>
                <option value="2時間30分">2時間30分</option>
                <option value="3時間">3時間</option>
                <option value="3時間30分">3時間30分</option>
                <option value="4時間">4時間</option>
                <option value="4時間30分">4時間30分</option>
                <option value="5時間">5時間</option>
                <option value="5時間30分">5時間30分</option>
                <option value="6時間">6時間</option>
                <option value="6時間30分">6時間30分</option>
                <option value="7時間">7時間</option>
                <option value="7時間30分">7時間30分</option>
                <option value="8時間">8時間</option>
                <option value="8時間30分">8時間30分</option>
            </select>

            <div class="point_of_hidden_note">
                <p class="input_title">あなたのお名前（必須）<a id="hidden_note_sw01">説明を表示</a></p>
                <div class="hidden_note" id="hidden_note01">
                    <p>本名を入力する必要はありませんが、参加者があなただとわかる名前を入力してあげてください。</p>
                </div>
            </div><!-- .point_of_hidden_note -->
            <input type="text" name="master_name" id="master_name" class="required count16" value="" placeholder="" />

            <div class="point_of_hidden_note">
                <p class="input_title">パスワード<a id="hidden_note_sw02">説明を表示</a></p>
                <div class="hidden_note" id="hidden_note02">
                    <p>イベントにはランダムなURLが割り当てられますが、そのURLをブラウザに打ち込めば誰でも見ることができます。</p>
                    <p>メンバー以外に見られないようにするには、パスワードを設定してください。</p>
                    <p>パスワードを設定しない場合は、空欄にしておいてください。</p>
                </div>
            </div><!-- .point_of_hidden_note -->

            <input type="text" name="pass" id="pass" class="byte_alphanumeric count16" value="" placeholder="半角英数16文字以内" />

            <div class="point_of_hidden_note">
                <p class="input_title">メールアドレス<a id="hidden_note_sw03">説明を表示</a></p>
                <div class="hidden_note" id="hidden_note03">
                    <p>メールアドレスを登録しておくと、作成したイベント情報（イベントページ/マスターページのURL、パスワード）の控えが送信されます。（入力必須ではありません）</p>
                    <p>また、参加メンバーがイベントページで参加の可否を登録するごとに、通知メールが届きます。</p>
                    <p>メールアドレスは、イベントに「日時決定」の操作をすると同時に破棄されます。Beengoがあなたのメールアドレスを永続的に保存することはありません。</p>
                    <p>※メールは「no-reply@beengo.cc」というアドレスから届きます。このアドレスからの受信を許可しておいてください。（とくに携帯メールアドレスを使用する場合は注意してください）</p>
                </div>
            </div><!-- .point_of_hidden_note -->

            <input type="text" name="mail" id="mail" class=" mail count64" value="" placeholder="お間違えのないように！" />

            <p class="input_title">イベントの詳細、参加者へのコメントなど</p>
            <textarea name="description" id="description" class="count1280" rows="10" cols="40" placeholder=""></textarea>

        </div><!--<input_event_info>-->

        <div id="cal_area">

            <div class="point_of_hidden_note">
                <p class="input_title">日時候補<a id="hidden_note_sw04">説明を表示</a></p>
                <div class="hidden_note" id="hidden_note04">
                    <p>カレンダーで候補の日付を選択し（複数同時に選択可）、「時間指定」で時間を指定し（4つまで同時に指定可）、「日時候補を追加」ボタンを押してください。（「時間指定」は必須ではありません）</p>
                    <p>たとえば、カレンダーで「4月1日」と「4月2日」を選択し、「時間指定1」で「13:00」、「時間指定2」で「14:00」を指定して「日時候補を追加」ボタンを押した場合、「4月1日 13:00」「4月1日 14:00」「4月2日 13:00」「4月2日 14:00」の4つの日時を同時に追加できます。</p>
                    <p>「日時候補を追加」は何度でも実行できます。間違って追加した日時は取り消すこともできます。</p>
                </div>
            </div><!-- .point_of_hidden_note -->

            <div id="cal_switch_btn_area">
                <a id="pre_month_btn">&lt;&lt;前の月</a>
                <a id="nxt_month_btn">次の月&gt;&gt;</a>
            </div>

            <div id="insert_cal_area"></div>

        </div><!--<cal_area>-->

        <div id="time_select_area">

            <div class="time_select">

                <p class="input_title">時間指定1</p>

                <input type="radio" name="time_OnOff01" id="time_off01" value="off" checked /><label for="time_off01">無効</label>
                <input type="radio" name="time_OnOff01" id="time_on01" value="on" /><label for="time_on01">有効</label>

                <p>
                    <select name="hour01" id="hour01" disabled>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="00">00</option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                    </select>
                    <span class="colon">:</span>
                    <select name="min01" id="min01" disabled>
                        <option value="00">00</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                </p>

            </div>

            <div class="time_select">

                <p class="input_title">時間指定2</p>

                <input type="radio" name="time_OnOff02" id="time_off02" value="off" checked disabled /><label for="time_off02">無効</label>
                <input type="radio" name="time_OnOff02" id="time_on02" value="on" disabled /><label for="time_on02">有効</label>

                <p>
                    <select name="hour02" id="hour02" disabled>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="00">00</option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                    </select>
                    <span class="colon">:</span>
                    <select name="min02" id="min02" disabled>
                        <option value="00">00</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                </p>

            </div>

            <div class="time_select">

                <p class="input_title">時間指定3</p>

                <input type="radio" name="time_OnOff03" id="time_off03" value="off" checked disabled /><label for="time_off03">無効</label>
                <input type="radio" name="time_OnOff03" id="time_on03" value="on" disabled /><label for="time_on03">有効</label>

                <p>
                    <select name="hour03" id="hour03" disabled>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="00">00</option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                    </select>
                    <span class="colon">:</span>
                    <select name="min03" id="min03" disabled>
                        <option value="00">00</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                </p>

            </div>

            <div class="time_select">

                <p class="input_title">時間指定4</p>

                <input type="radio" name="time_OnOff04" id="time_off04" value="off" checked disabled /><label for="time_off04">無効</label>
                <input type="radio" name="time_OnOff04" id="time_on04" value="on" disabled /><label for="time_on04">有効</label>

                <p>
                    <select name="hour04" id="hour04" disabled>
                        <option value="08">08</option>
                        <option value="09">09</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="00">00</option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                        <option value="03">03</option>
                        <option value="04">04</option>
                        <option value="05">05</option>
                        <option value="06">06</option>
                        <option value="07">07</option>
                    </select>
                    <span class="colon">:</span>
                    <select name="min04" id="min04" disabled>
                        <option value="00">00</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                </p>

            </div>

        </div><!--<time_select_area>-->

        <input type="button" id="btn_add" class="btn_orange" value="日時候補を追加">

        <div id="added_datetime_area">

            <table id="added_datetime_table"></table>

        </div>

        <p class="note_c14">入力内容をよくご確認のうえ、<br />「イベントを作成」ボタンを押してください。<br />（確認画面は表示されません）</p>
        <input type="submit" class="btn_red" value="イベントを作成" />

        <!-- 二重ポスト、CSRF対策 -->
        <input type="hidden" name="token" value="<?php echo $token ?>" />

    </form>

</div><!--<create_event_area>-->

<?php include 'footer.php' ?>

</body>
</html>