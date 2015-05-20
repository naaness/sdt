/**
 * Created by nesto_000 on 19/03/15.
 */
var server = "http://"+window.location.hostname;
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
    var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
    var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    // At least Safari 3+: "[object HTMLElementConstructor]"
    var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
    var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6

    // Cinco pasos
    $("#alert-notification").click(function(){

        $.post(server+'/notifications/wasSeen', 'id=azx',  function(datos){
            if (datos!='None'){
                $( "#cont-notification" ).hide();
            }
        }).fail(function() {
            console.log("error de conexion");
        });

        if(isChrome){
            event.preventDefault();
        }
    });

    // verificar cada 5 segundos si hay notificaciones y alertas nuevas
    setInterval(newNotificationsAlerts, 10000);
    newNotificationsAlerts();

});

var newNotificationsAlerts = function(){
    var con = $("#cont-alert").html();
    if(con==''){
        con=-1;
    }
    var con2 = $("#cont-notification").html();
    if(con2==''){
        con2=-1;
    }

    $.post(server+'/notifications/news','cont='+con+'&cont2='+con2,function(datos){
        if(typeof datos.notifications != 'undefined'){
            $("#content-notification").html('');
            $("#content-notification").html(datos.notifications.html);
            $("#cont-notification").html();
            $("#cont-notification").html(datos.notifications.count);
            if (datos.notifications.count>0){
                $("#cont-notification").show();
            }else{
                $( "#cont-notification" ).hide();
            }
        }
        if(typeof datos.alerts != 'undefined'){
            $("#content-alert").html('');
            $("#content-alert").html(datos.alerts.html);
            $("#cont-alert").html();
            $("#cont-alert").html(datos.alerts.count);
            if (datos.alerts.count>0){
                $("#cont-alert").show();
            }else{
                $( "#cont-alert" ).hide();
            }
        }
    }, 'json').fail(function() {
        console.log("error de conexion");
    });
}

