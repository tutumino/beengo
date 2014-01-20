// Enterキーでのsubmitを禁止
$(document).ready(function () {
    $('input, textarea[readonly]').not($('input[type="button"], input[type="submit"]')).keypress(function (e) {
        if (!e) {
            var e = window.event;
        }
        // alert(e.keyCode);
        if (e.keyCode == 13) {
            return false;
        }
    });
});
