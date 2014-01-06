$(function() {

    $('form').submit(function() {
    
        //エラーの初期化
        var errFlag = 0;
        var errMsg = '';

        // alert($('input[name="fix"]:checked').val());
        if ($('input[name="fix"]:checked').val() == undefined) {
            errMsg += '日時が選択されていません。\n';
                errFlag = 1;
        }

        // alert($('input[name="map_type"]:checked').val());
        if ($('input[name="map_type"]:checked').val() != 'map_type3' && ($('#map_location').val() == '' || $('#map_location').val() == mapLocationSample)) {
            errMsg += '地図の検索欄が入力されていません。\n';
                errFlag = 1;
        }

        if (errFlag == 1) {

            alert(errMsg);
            return false;

        } else {

            // valueがサンプルテキストのままの場合、valueを空に
            $(':input').each(function() {
                if ($(this).val() == mapLocationSample) {
                    $(this).val('');
                }
            })

            return true;
        }

    })
    
})
