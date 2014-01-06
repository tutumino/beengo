// カレンダー
var calNow = new Date();
var calThisYear = calNow.getFullYear();
var calThisMonth = calNow.getMonth() + 1;
var calYear = calThisYear;
var calMonth = calThisMonth;
var calToday = calNow.getDate();
var calDays = new Array('日', '月', '火', '水', '木', '金', '土');
var calMonthDays = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
// カレンダー書き出し関数
function showCal(n) {
    calMonth += n;
    if (calMonth == 0) {
        calYear--;
        calMonth = 12;
    } else if (calMonth == 13) {
        calYear++;
        calMonth = 1;
    } else if (calMonth == -1) {
        calYear--;
        calMonth = 11;
    } else if (calMonth == -2) {
        calYear--;
        calMonth = 10;
    }
    // 表示月の1日の曜日を取得
    var calFirstDay = new Date(calYear, calMonth - 1, 1).getDay();
    // 表示月の最終日代入
    var calDateMax = calMonthDays[calMonth - 1];
    // 閏年の場合の処理
    if (calMonth == 2 && (calYear % 4 == 0 && calYear % 100 != 0) || calYear % 400 == 0) {
        calDateMax = 29;
    }
    // カレンダーの出力
    $('#insert_cal_area').append('<table>\n<tr><th class="cal_head" colspan=7>' + calYear + '年&nbsp;' + calMonth + '月' + '</th></tr>\n<tr class="cal_days_row"></tr>\n');
    for (i = 0; i <= 6; i++) {
        $('#insert_cal_area table:last-child() tr:last-child()').append('<th>' + calDays[i] + '</th>');
    };
    var calColNo = 0;
    if (calFirstDay > 0) {
        $('#insert_cal_area table:last-child()').append('<tr>');
        for (; calColNo < calFirstDay; calColNo++) {
            $('#insert_cal_area table:last-child() tr:last-child()').append('<td class="cal_emptyTd">&nbsp;</td>');
        }
    }
    for (i = 1; i <= calDateMax; i++) {
        if (calColNo == 0) {
            $('#insert_cal_area table:last-child()').append('<tr>');
        }
        // calMonth（月）とi（日）が1桁だったら、10の位に0をつける処理（idをyyyymmddに揃えるため）
        var dblFgrsM;
        var dblFgrsD;
        if (calMonth < 10) {
            dblFgrsM = '0' + calMonth;
        } else {
            dblFgrsM = calMonth;
        }
        if (i < 10) {
            dblFgrsD = '0' + i;
        } else {
            dblFgrsD = i;
        }
        $('#insert_cal_area table:last-child() tr:last-child()').append('<td id="' + calYear + dblFgrsM + dblFgrsD + '" class="cal_off">' + i + '</td>');
        if (calColNo == 6) {
            $('#insert_cal_area table:last-child() tr:last-child()').append('</tr>');
            calColNo = 0;
        } else {
            calColNo++;
        }
    }
    if (calColNo != 0) {
        for (; calColNo <= 6; calColNo++) {
            $('#insert_cal_area table:last-child() tr:last-child()').append('<td class="cal_emptyTd">&nbsp;</td>');
        }
        $('#insert_cal_area table:last-child() tr:last-child()').append('</tr>\n</table>');
    }
    // すでに「追加」済みの日付のclassをcal_addedにする
    for (i = 0; i < addedDateIdArr.length; i++) {
        $('#' + addedDateIdArr[i].substr(0, 8)).removeClass().addClass('cal_added');
    }
    // すでに「選択」されている（onになっている）日付のclassをcal_onにする
    for (i = 0; i < calOnIdArr.length; i++) {
        $('#' + calOnIdArr[i]).removeClass().addClass('cal_on');
    }
}
// カレンダー関数呼び出し
$(function() {
    showCal(0);
    showCal(1);
    showCal(1);
    $('#pre_month_btn').click(function() {
        $('#insert_cal_area').empty();
        showCal(-3);
        showCal(1);
        showCal(1);
    });
    $('#nxt_month_btn').click(function() {
        $('#insert_cal_area').empty();
        showCal(-1);
        showCal(1);
        showCal(1);
    });
})
// カレンダーの日付のクリックで、on/off切り替え（そのtdのidを配列calOnIdArrに代入）
var calOnIdArr = new Array();
$(function() {
    $(document).on('click', '#insert_cal_area td:not(".cal_emptyTd")', function() {
        var getDateId = $(this).attr('id');
        // 配列にそのidが存在しないなら追加。存在するなら削除（存在しない場合-1を返す）およびclassの変更
        var inArray = $.inArray(getDateId, calOnIdArr);
        if (inArray == -1) {
            calOnIdArr.push(getDateId);
            $(this).removeClass();
            $(this).addClass('cal_on');
        } else {
            calOnIdArr.splice(inArray, 1);
            $(this).removeClass();
            if (addedDateIdArr.length == 0) {
                $(this).addClass('cal_off');
            } else {
                for (i = 0; i < addedDateIdArr.length; i++) {
                    if (addedDateIdArr[i].substr(0, 8) == getDateId) {
                        $(this).addClass('cal_added');
                        break;
                    } else {
                        $(this).addClass('cal_off');
                    }
                }                
            }
        }
    });
})

// 「追加」ボタンで日時を追加
var addDateIdArr = new Array();
var addedDateIdArr = new Array();
$(function () {
    $('#btn_add').click(function() {
        // 日付が選択されていなければアラート
        if (calOnIdArr == '') {
            alert('カレンダーの日付をクリックして、日付を選択してください。');
        // 選択されていれば、以下「追加」処理
        } else {
            // 「時間」のインプット値を取得
            var timesArr = new Array();
            var timeOnOff01 = $('input:radio[name="time_OnOff01"]:checked').val();
            if (timeOnOff01 == 'on') {
                timesArr[0] = $('#hour01').val() + $('#min01').val();
            }
            var timeOnOff02 = $('input:radio[name="time_OnOff02"]:checked').val();
            if (timeOnOff02 == 'on') {
                timesArr[1] = $('#hour02').val() + $('#min02').val();
            }
            var timeOnOff03 = $('input:radio[name="time_OnOff03"]:checked').val();
            if (timeOnOff03 == 'on') {
                timesArr[2] = $('#hour03').val() + $('#min03').val();
            }
            var timeOnOff04 = $('input:radio[name="time_OnOff04"]:checked').val();
            if (timeOnOff04 == 'on') {
                timesArr[3] = $('#hour04').val() + $('#min04').val();
            }
            // trにつけるidを用意（idは「yyyymmddhhmm」の12桁となる。時間指定がなければidは「yyyymmdd--」となる）と、カレンダーの日付にclassを付ける処理
            for (i = 0, j = 0; i < calOnIdArr.length; i++) {
                if (timeOnOff01 == 'on') {
                    for (k = 0; k < timesArr.length; j++, k++) {
                        addDateIdArr[j] = calOnIdArr[i] + timesArr[k];
                    }
                } else {
                    addDateIdArr[i] = calOnIdArr[i] + '--';
                }
                $('#' + calOnIdArr[i]).removeClass().addClass('cal_added');
            }
            calOnIdArr = [];
            addDateIdArr.sort();
            outloop:
            for (i = 0; i < addDateIdArr.length; i++) {
                var year = addDateIdArr[i].substr(0, 4);
                var month = addDateIdArr[i].substr(4, 2);
                var date = addDateIdArr[i].substr(6, 2);
                var days = new Array('（日）', '（月）', '（火）', '（水）', '（木）', '（金）', '（土）');
                var day = new Date(year, month - 1, date).getDay();
                // 「時間」が指定されていれば（すなわちid文字列が12桁であれば）変数timeに「時間」（表示用）を代入
                if (addDateIdArr[i].length == 12) {
                    
                    var time = addDateIdArr[i].substr(8, 2) + ':' + addDateIdArr[i].substr(10, 2);
                } else {
                    var time = '';
                }
                // すでに同じ日時が「追加」されていないかを確認する処理（追加されていればスキップ）
                var trNum = $('#added_datetime_table tbody').children().length;
                var trId;
                for (j = 1; j <= trNum; j++) {
                    trId = $('#added_datetime_table tr:nth-child(' + j + ')').attr('id');
                    if (trId == addDateIdArr[i]) {
                        alert(year + '年' + month + '月' + date + '日 ' + time + 'は、すでに追加されています。');
                        continue outloop;
                    }
                }
                // 日時を書き出す
                var appendTr = '<tr id="' + addDateIdArr[i] + '" class="added_datetime">\n</tr>\n';
                var appendTd = '<td class="added_datetime"><span class="year">' + year + '年' + '</span>' + month + '月' + date + '日' + days[day] + '&nbsp;' + time + '</td>\n<td><div class="del_btn">この日時を取り消す</div></td>\n<td><input class="hidden" type="hidden" name="datetime[]" value="' + addDateIdArr[i] + '" /></td>';
                if (trNum == 0) {
                    $('#added_datetime_table').append(appendTr);
                    $('#' + addDateIdArr[i]).hide().append(appendTd).fadeIn(500);
                } else {
                    for (j = 1; j <= trNum; j++) {
                        trId = $('#added_datetime_table tr:nth-child(' + j + ')').attr('id');
                        if (addDateIdArr[i] > trId && j == trNum) {
                            $('#added_datetime_table').append(appendTr);
                            $('#' + addDateIdArr[i]).hide().append(appendTd).fadeIn(500);
                        } else if (addDateIdArr[i] > trId) {
                            continue;
                        } else if (addDateIdArr[i] < trId) {
                            $('#' + trId).before(appendTr);
                            $('#' + addDateIdArr[i]).hide().append(appendTd).fadeIn(500);
                            break;
                        }
                    }
                }
                // 書き出した日時のidを配列に追加
                addedDateIdArr.push(addDateIdArr[i]);
            }
            addedDateIdArr.sort();
            addDateIdArr = [];
        }
    });
});

// 「削除」ボタンクリック
$(function () {
    $(document).on('click', '.del_btn', function() {
        var delTrId = $(this).parents('tr').attr('id');
        var delTrId8Fgrs = delTrId.substr(0, 8);
        // 配列addedDateIdArrから該当idを削除
        addedDateIdArr.splice($.inArray(delTrId, addedDateIdArr), 1);
        // 配列addedDateIdArrが空なら（つまりこれから削除するのが最後の一つなら）、カレンダーの該当日付のclassをcal_offにする
        if (addedDateIdArr.length == 0) {
            $('#' + delTrId8Fgrs).removeClass().addClass('cal_off');
        // この日付を削除しても、同一日付の別時間が残っているなら、カレンダーの該当日付のclassはcal_addedにしておく。一つも残っていないなら、cal_offにする。
        } else {
            for (i = 0; i < addedDateIdArr.length; i++) {
                if (addedDateIdArr[i].substr(0, 8) == delTrId8Fgrs) {
                    $('#' + delTrId8Fgrs).removeClass().addClass('cal_added');
                    break;
                } else {
                    $('#' + delTrId8Fgrs).removeClass().addClass('cal_off');
                }
            }
        }
        // trを削除する
        $(this).parents('tr').fadeOut(500, function() { $(this).remove(); });
    });
})