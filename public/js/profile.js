/**
 * Created by nesto_000 on 1/04/15.
 */
$(document).ready(function(){

    var offset1 = $("#navbar_color").offset();
    var offset2 = $("#body_color").offset();
    var ancho = 80;
    $("#dummy1").height(ancho);
    $("#dummy2").height(ancho+30);
    var cp1  = Raphael.colorpicker(offset1.left,offset1.top +40,ancho,$("#navbar_color").val());
    var cp2 = Raphael.colorpicker(offset2.left,offset2.top +40 ,ancho,$("#body_color").val());

    cp1.onchange = function (clr) {
        console.log(clr);
        $("#navbar_color").val(clr);
        $("#navbar-color").css('background-color',clr);

    };
    cp2.onchange = function (clr) {
        console.log(clr);
        $("#body_color").val(clr);
        $("#body-color").css('background-color',clr);
    };
});