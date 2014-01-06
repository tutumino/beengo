
function inputSample(selecter, sample) {

    $(function() {
        $(selecter).val(sample).css('color', '#bbbbbb').one('focus', function() {
            $(this).val('').css('color', '#000000');
        }).blur(function() {
            if ($(this).val() == '') {
                $(this).val(sample).css('color', '#bbbbbb').one('focus', function() {
                    $(this).val('').css('color', '#000000');
                });
            }
        });
    })
    
}
    