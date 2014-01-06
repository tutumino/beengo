$(function() {

    $('form').submit(function() {
    
        //エラーの初期化
        var errFlag = 0;
        var errMsg = '';

        $('.required').each(function() {
            // alert();
            if ($(this).val() == '' || $(this).val() == menberNameSample) {
                var inputName = $(this).attr('id');
                switch (inputName) {
                    case 'member_name':
                        inputName = 'あなたのお名前';
                        break;
                }
                errMsg += inputName + 'が入力されていません。\n';
                errFlag = 1;
            }
        });

        if (errFlag == 1) {

            alert(errMsg);
            return false;

        } else {

            // valueがサンプルテキストのままの場合、valueを空に
            $(':input').each(function() {
                if ($(this).val() == menberNameSample || $(this).val() == commentSample) {
                    $(this).val('');
                }
            })

            return true;
        }

    })
    
})
