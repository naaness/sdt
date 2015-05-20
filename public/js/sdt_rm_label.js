$(document).ready(function(){

    var offset1 = $("#example1").offset();
    var offset2 = $("#example2").offset();
    var ancho = $("#example1").width();
    $("#content-colors").height(ancho);
    var cp  = Raphael.colorpicker(offset1.left,offset1.top +30,ancho,$("#color").val());
    var cp2 = Raphael.colorpicker(offset2.left,offset2.top +30,ancho,$("#b_color").val());

    cp.onchange = function (clr) {
        $("#example1").css('color',clr);
        $("#example2").css('color',clr);
        $("#color").val(clr);
    };
    cp2.onchange = function (clr) {
        var new_color = color_light(clr);
        $("#example1").css('background-color',clr);
        $("#example2").css('background-color',new_color);
        $("#b_color").val(clr);
        $("#b_color_checked").val(new_color);
    };
});

var color_light = function (color){
    //voy a extraer las tres partes del color
    var rojo = color.substring(1, 3);
    var verde = color.substring(3, 5);
    var azul = color.substring(5);

    //voy a convertir a enteros los string, que tengo en hexadecimal
    var introjo = parseInt(rojo, 16);;
    var intverde =parseInt(verde, 16);
    var intazul = parseInt(azul, 16);

    //aumentar la luz para quede mas claro el color
    var light=100;
    introjo+=light;
    if (introjo>255) introjo=255;
    intverde+=light;
    if (intverde>255) intverde=255;
    intazul+=light;
    if (intazul>255) intazul=255;

    // Volver al numero hexadecimal el color
    rojo = introjo.toString(16);
    verde = intverde.toString(16);
    azul = intazul.toString(16);
    return "#"+rojo+verde+azul;
}