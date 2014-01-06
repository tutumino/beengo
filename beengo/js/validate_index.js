$(function() {

    $('form').submit(function() {
    
        //エラーの初期化
        var errFlag = 0;
        var errMsg = '';

        $('.required').each(function() {
            // alert();
            if ($(this).val() == '' || $(this).val() == titleSample || $(this).val() == nameSample) {
                var inputName = $(this).attr('id');
                switch (inputName) {
                    case 'title':
                        inputName = 'イベント名';
                        break;
                    case 'master_name':
                        inputName = 'あなたのお名前';
                        break;
                }
                errMsg += inputName + 'が入力されていません。\n';
                errFlag = 1;
            }
        });

        $('.byte_alphanumeric').each(function() {
            if ($(this).val() != passSample && $(this).val().match(/[^0-9A-Za-z]+/)) {
                var inputName = $(this).attr('id');
                switch (inputName) {
                    case 'pass':
                        inputName = 'パスワード';
                        break;
                }
                errMsg += inputName + 'は半角英数で入力してください。\n';
                errFlag = 1;
            }
        });

        if ($('#pass').val() != '' && $('#pass').val() != passSample && $('#pass2').val() != $('#pass').val()) {
            errMsg += 'パスワード（再入力）がパスワードと一致しません。\n';
            errFlag = 1;
        }

        if ($('#added_datetime_table tbody').size() == 0) {
            errMsg += '日時候補は1つ以上追加する必要があります。\n';
            errFlag = 1;
        }

        if (errFlag == 1) {

            alert(errMsg);
            return false;

        } else {

            // valueがサンプルテキストのままの場合、valueを空に
            $(':input').each(function() {
                if ($(this).val() == titleSample || $(this).val() == nameSample || $(this).val() == descSample || $(this).val() == passSample || $(this).val() == passSample2) {
                    $(this).val('');
                }
            })

            return true;
        }

    })
    
})
