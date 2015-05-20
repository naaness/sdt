/**
 * Created by nesto_000 on 11/04/15.
 */
var showSpinner = function(obj){
    var offsetXY = obj.offset();
    var width = obj.width();
    //obj.css('opacity','0.5');
    $("#spinner").css("z-index",99999999);
    $("#spinner").css("top",0);
    $("#spinner").css("left",0);
    $("#spinner").offset({ top: offsetXY.top -55, left: (offsetXY.left+width/2-25) });
    var offset = $("#spinner").offset();
    $("#spinner").css("display", "");
}

var hideSpinner = function(obj){
    obj.css('opacity','1');
    $("#spinner").css("display", "none");
}