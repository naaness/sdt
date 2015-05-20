server = "http://"+window.location.hostname;
$(document).ready(function(){

    $("#body-daily-planning").html('');
    // Cinco pasos
   	$.post(server+'/users/fiveSteps', 'id=my',  function(datos){
		if (datos!='None') {
            var html = '';
            var da = datos.stepDays;
            var mode = 'show';
            var cont1 = 0;
            var cont2 = 0;
            for (var i = 0; i <da.length; i++) {
                if(da[i].head=="1"){
                    html+='<tr class="active">';
                    html+='<td><strong>'+da[i].order_r+'.</strong></td>';
                    html+='<td><strong>'+da[i].message+'.<small>'+da[i].submessage+'.</small></strong></td>';
                    html+='<td></td>';
                    html+='</tr>';
                }else{
                    var checked = '';
                    if(da[i].checked==1){
                        checked = 'checked';
                    }
                    html+='<tr>';
                    html+='<td><strong> -</strong></td>';
                    html+='<td><em>'+da[i].message+'.<small>'+da[i].submessage+'.</small></em></td>';
                    html+='<td><input type="checkbox" id="rm-cinco-pasos_'+da[i].id+'" value="0" aria-label="Checkbox without label text" '+checked+'></td>';
                    html+='</tr>';
                    if(da[i].checked==1){
                        cont2+=1;
                    }
                    cont1+=1;
                }
            }
            $("#body-daily-planning").html(html);

			if (datos['step_of_day']==1) {
                if(cont1==cont2){
                    mode='hide';
                }
				$("#myModalFiveSteps").modal(mode);
			}else{
				$("#sdt-cinco-pasos").text('Activar');
			}

            $("input[id^='rm-cinco-pasos']").click(function(){
                var id = $(this).attr("id");
                id = id.split("_");
                id = id[1];
                var c = this.checked ? 1 : 0;
                $.post(server+'/users/fiveStepsUpdate', 'id='+id+'&valor='+c,  function(datos){

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.log( "Request failed: " + textStatus + "  --- " + errorThrown);
                });
            });
		};
	},'json' ).fail(function(jqXHR, textStatus) {
        console.log( "Request failed: " + textStatus);
    });

	$( "#rm-cinco-text-12" ).focusout(function() {
		$.post(server+'/users/fiveStepsText', 'text='+$(this).val(),  function(datos){
 		}).fail(function(jqXHR, textStatus, errorThrown) {
            console.log( "Request failed: " + textStatus + "  --- " + errorThrown);
        });
	});
	$("#ver-pasos-dia").click(function(){
        var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
        // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
        var isFirefox = typeof InstallTrigger !== 'undefined';   // Firefox 1.0+
        var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
        // At least Safari 3+: "[object HTMLElementConstructor]"
        var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
        var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6

		$("#myModalFiveSteps").modal('show');
        if(isChrome){
            event.preventDefault();
        }
	});

	$("#sdt-cinco-pasos").click(function(){
		$.post(server+'/users/fiveStepsOnOff', function(datos){
			$("#sdt-cinco-pasos").text(datos);
 		}).fail(function(jqXHR, textStatus, errorThrown) {
            console.log( "Request failed: " + textStatus + "  --- " + errorThrown);
        });
	});
});