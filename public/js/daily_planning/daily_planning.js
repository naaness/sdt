/**
 * Created by nesto_000 on 4/05/15.
 */
$(document).ready(function() {
    $("#dp-order-r").text($("#order_r").val()+".");
    $("#dp-message").text($("#message").val()+".");
    $("#dp-submessage").text($("#submessage").val()+".");


    $("#order_r").keyup(function(){
        $("#dp-order-r").text($("#order_r").val()+".");
    });
    $("#order_r").change(function(){
        $("#dp-order-r").text($("#order_r").val()+".");
    });
    $("#message").keyup(function(){
        $("#dp-message").text($("#message").val()+".");
    });
    $("#submessage").keyup(function(){
        $("#dp-submessage").text($("#submessage").val()+".");
    });
});