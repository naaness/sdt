/**
 * Created by nesto_000 on 9/04/15.
 */
var server = "http://"+window.location.hostname;
$(document).ready(function() {
    $('#myTabProfile a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
});