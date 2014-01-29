$(function() {

    $('form').submit(function() {
    
        //エラーの初期化
        var errFlag = 0;
        var errMsg = '';

        if ($('.required').size()) {

            $('.required').each(function() {
                // alert();
                if ($(this).val() == '') {
                    var inputName = $(this).attr('id');
                    switch (inputName) {
                        case 'title':
                            inputName = '「イベント名」';
                            break;
                        case 'master_name':
                            inputName = '「あなたのお名前」';
                            break;
                        case 'member_name':
                            inputName = '「あなたのお名前」';
                            break;
                    }
                    errMsg += inputName + 'が入力されていません。\n';
                    errFlag = 1;
                }
            });

        }

        if ($('.count16').size()) {

            $('.count16').each(function() {
                if ($(this).val().length > 16) {
                    var inputName = $(this).attr('id');
                    switch (inputName) {
                        case 'master_name':
                            inputName = '「あなたのお名前」';
                            break;
                        case 'pass':
                            inputName = '「パスワード」';
                            break;
                        case 'member_name':
                            inputName = '「あなたのお名前」';
                            break;
                    }
                    errMsg += inputName + 'は16文字以内で入力してください。\n';
                    errFlag = 1;
                }
            })

        }

        if ($('.count64').size()) {

            $('.count64').each(function() {
                if ($(this).val().length > 64) {
                    var inputName = $(this).attr('id');
                    switch (inputName) {
                        case 'title':
                            inputName = '「イベント名」';
                            break;
                        case 'map_location':
                            inputName = '地図の検索欄';
                            break;
                    }
                    errMsg += inputName + 'は64文字以内で入力してください。\n';
                    errFlag = 1;
                }
            })

        }

        if ($('.count512').size()) {

            $('.count512').each(function() {
                if ($(this).val().length > 512) {
                    var inputName = $(this).attr('id');
                    switch (inputName) {
                        case 'comment':
                            inputName = '「備考、メッセージ」欄';
                            break;
                    }
                    errMsg += inputName + 'は512文字以内で入力してください。\n';
                    errFlag = 1;
                }
            })

        }

        if ($('.count1280').size()) {

            $('.count1280').each(function() {
                if ($(this).val().length > 1280) {
                    var inputName = $(this).attr('id');
                    switch (inputName) {
                        case 'description':
                            inputName = '「イベントの概要、詳細、コメントなど」';
                            break;
                        case 'description2':
                            inputName = '「イベントの概要、詳細、コメントなど」';
                            break;
                    }
                    errMsg += inputName + 'は1280文字以内で入力してください。\n';
                    errFlag = 1;
                }
            })

        }
            
        if ($('.byte_alphanumeric').size()) {

            $('.byte_alphanumeric').each(function() {
                if ($(this).val().match(/[^0-9A-Za-z]+/)) {
                    var inputName = $(this).attr('id');
                    switch (inputName) {
                        case 'pass':
                            inputName = '「パスワード」';
                            break;
                    }
                    errMsg += inputName + 'は半角英数で入力してください。\n';
                    errFlag = 1;
                }
            });

        }
            
        // if ($('#pass2').size()) {

        //     if ($('#pass').val() != '' && $('#pass2').val() != $('#pass').val()) {
        //         errMsg += '「パスワード（再入力）」が「パスワード」と一致しません。\n';
        //         errFlag = 1;
        //     }

        // }
            
        if ($('#added_datetime_table').size()) {

            if ($('#added_datetime_table tbody').size() == 0) {
                errMsg += '「日時候補」は1つ以上追加する必要があります。\n';
                errFlag = 1;
            }

        }
            

        if ($('input[name="fix"]').size()) {
            // alert($('input[name="fix"]:checked').val());
            if ($('input[name="fix"]:checked').val() == undefined) {
                errMsg += '日時が選択されていません。\n';
                    errFlag = 1;
            }
        }
            
        if ($('input[name="map_type"]').size()) {

            // alert($('input[name="map_type"]:checked').val());
            if ($('input[name="map_type"]:checked').val() != 'map_type3' && $('#map_location').val() == '') {
                errMsg += '地図の検索欄が入力されていません。\n';
                    errFlag = 1;
            }

        }
            

        if (errFlag == 1) {

            alert(errMsg);
            return false;

        } else {

            return true;

        }

    })
    
})
