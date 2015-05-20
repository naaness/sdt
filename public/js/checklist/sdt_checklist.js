
/* enable strict mode */
"use strict";
var server = "http://"+window.location.hostname;
var offset = null;
var offsetG = null;
var nameday  = $("#ch-ntoday").val();
var ntoday = nameday ;
var last_popover = null ;
var last_unit_move = null ;
var priority = [];
$(document).ready(function(){

	$('#unidadTiempo').change(function(){
		var aaa = $("#tb tr:eq(0) td:eq(2) .clone");
		aaa.attr("class", "drag clone orange "+$('#unidadTiempo').val()+" vacio");
	});

	//Validar valores para tareas periodicas.
	$('#submit_respuesta').attr("disabled", true);
	$('#respuesta_si').click(function(){
		$("#ch-respuesta").val("1");
		$('#submit_respuesta').attr("disabled", false);
		$('#ch-resp-comentario').fadeOut();
	});
	$('#respuesta_no').change(function(){
		$("#ch-respuesta").val("3");
		var valor1 = $('#ch-resp-comentario').val();
		$('#ch-resp-comentario').fadeIn();
		if (valor1.trim()!="") {
			$('#submit_respuesta').attr("disabled", false);
		}else{
			$('#submit_respuesta').attr("disabled", true);
		}
	});
	$('#ch-resp-comentario').keyup(function(){
		var valor1 = $('#ch-resp-comentario').val();
		if (valor1.trim()!="") {
			$('#submit_respuesta').attr("disabled", false);
		}else{
			$('#submit_respuesta').attr("disabled", true);
		}
	});

	$("#submit_respuesta").click(function(){
		$('#submit_respuesta').attr("disabled", true);
		$.post(server+'/tasks/giveAnswer','taread='+$("#ch-RespTareaId").val()+'&respues='+$("#ch-respuesta").val()+"&comentario="+$("#ch-resp-comentario").val())
		.done(function() {
		   actualizarVistaCH();
		   $('#myModalRespTarea').modal('hide');
		})
		.fail(function() {
		    console.log("error de conexion");
		    $('#myModalRespTarea').modal('hide');
		});
	});
	
	$('#ch_contenido').on('click', '#ch_btn_hoy', function(){
        showSpinner($("#ch_contenido"));
		$.post(server+'/checklist/sdtChecklistView', 'date='+$("#ch-today").val()+'&range='+$("#rangoVista").val() + parametrosCheclist(), function(datos){
            hideSpinner($("#ch_contenido"));
            $("#ch_contenido").html(datos);
			inicioCheckList();
			inicioFiltroCheckList();
 		}).fail(function() {
	   		console.log("error de conexion");
		});
	});

	$('#ch_contenido').on('click', '#ch_btn_left', function(){
		var fecha =  $("#ch-back-range").val();
		var fecha = operateDate(fecha,-2);
        showSpinner($("#ch_contenido"));
		$.post(server+'/checklist/sdtChecklistView', 'date='+fecha+'&range='+$("#rangoVista").val() + parametrosCheclist(), function(datos){
            hideSpinner($("#ch_contenido"));
            $("#ch_contenido").html(datos);
			inicioCheckList();
			inicioFiltroCheckList();
 		}).fail(function() {
	   		console.log("error de conexion");
		});
	});

	$('#ch_contenido').on('click', '#ch_btn_right', function(){
		var fecha =  $("#ch-next-range").val();
		var fecha = operateDate(fecha,0);
        showSpinner($("#ch_contenido"));
		$.post(server+'/checklist/sdtChecklistView', 'date='+fecha+'&range='+$("#rangoVista").val() + parametrosCheclist(), function(datos){
            hideSpinner($("#ch_contenido"));
            $("#ch_contenido").html(datos);
			inicioCheckList();
			inicioFiltroCheckList();
 		}).fail(function() {
	   		console.log("error de conexion");
		});
	});

	$('#ch_contenido').on('change', '#rangoVista', function(){
		actualizarVistaCH();
	});

    showSpinner($("#ch_contenido"));
	$.post(server+'/checklist/sdtChecklistView', 'range=month' + parametrosCheclist(), function(datos){
        hideSpinner($("#ch_contenido"));
		$("#ch_contenido").html(datos);
		inicioCheckList();
		inicioFiltroCheckList();
		}).fail(function() {
   		console.log("error de conexion");
	});

	$('#ch_contenido').on('click', '#ch-mode-1', function(){
		$("#ch_contenido").attr('class','');
		$("div[id^='popover']").remove();
	});
	$('#ch_contenido').on('click', '#ch-mode-2', function(){
		$("#ch_contenido").attr('class','mode-add');
		$("div[id^='popover']").remove();
	});
	$('#ch_contenido').on('click', '#ch-mode-3', function(){
		$("#ch_contenido").attr('class','mode-delete');
		$("div[id^='popover']").remove();
	});
	$('#ch_contenido').on('click', '#ch-mode-4', function(){
		$("#ch_contenido").attr('class','mode-move');
		$("div[id^='popover']").remove();
	});

	$('#ch_contenido').on('click', '#tbl .filter-day', function(){
        var pos_edit = $(this).attr('class');
        var pos_edit = pos_edit.indexOf("editable-yes");
        if (pos_edit>-1){
            var _ut = $(this).find('.orange');
            if (_ut.length==0) {
                if ($("#ch_contenido").attr('class')=='mode-add') {
                    var id_td = $(this).attr('id');
                    id_td = id_td.split('_');
                    var day_num = id_td[2]-4;

                    var id_temp = 'dummy'+Math.floor((Math.random() * 100000) + 1);
                    var unit_time = '<div class="drag orange popover-unit-time" id="'+id_temp+'">'+
                        '<div class="text-center">'+
                        '<span class="glyphicon glyphicon-minus circle"></span>'+
                        '</div>'+
                        '</div>';
                    unit_time = $(unit_time);
                    $(this).append(unit_time);
                    agregarPopover(unit_time);
                    showSpinner($("#ch_contenido"));
                    $.post(server+'/checklist/sdtAddUnitTime','dummy_id='+id_temp+"&newdate="+operateDate($("#ch-back-range").val(),day_num)+"&task_id="+id_td[0], function(datos){
                        if (datos!='None') {
                            hideSpinner($("#ch_contenido"));
                            $('#tr_'+datos['task_id']).find('.percent').text(datos['task_percent']);
                            $("#"+id_temp).addClass(datos['color']);
                            $("#"+id_temp).attr('id',datos['id']);
                            if (datos['task_change_status']) {
                                $('#tr_'+datos['task_id']).find('.glyphicon-ok-circle').attr('class','glyphicon glyphicon-ok-circle task-wait');
                            };
                        };
                    }, 'json').fail(function() {
                        console.log("error de conexion");
                    });

                }else if ($("#ch_contenido").attr('class')=='mode-move') {
                    if (last_unit_move) {
                        $(this).append(last_unit_move.find('.orange'));
                        last_unit_move.find('.orange').remove();

                        var barra = last_unit_move.find('.beginweek');
                        if (barra) {
                            barra.css('top','-6px');
                        };
                        _ut = $(this).find('.orange');
                        agregarPopover(_ut);

                        var id_td = $(this).attr('id');
                        id_td = id_td.split('_');
                        var day_num = id_td[2]-4;

                        var id_td_old = $(last_unit_move).attr('id');
                        id_td_old = id_td_old.split('_');
                        showSpinner($("#ch_contenido"));
                        $.post(server+'/checklist/sdtChangeDayStart','id='+_ut.attr('id')+"&newdate="+operateDate($("#ch-back-range").val(),day_num)+"&task_id="+id_td[0]+"&task_id_old="+id_td_old[0], function(datos){
                            if (datos!='None') {
                                hideSpinner($("#ch_contenido"));
                                $('#tr_'+datos['task_id']).find('.percent').text(datos['task_percent']);
                                $('#tr_'+datos['task_id_old']).find('.percent').text(datos['task_percent_old']);
                                $("#"+datos['id']).addClass(datos['color']);
                                if (datos['task_change_status']) {
                                    $('#tr_'+datos['task_id']).find('.glyphicon-ok-circle').attr('class','glyphicon glyphicon-ok-circle task-wait');
                                };
                                if (datos['task_change_status_old']) {
                                    $('#tr_'+datos['task_id_old']).find('.glyphicon-ok-circle').attr('class','glyphicon glyphicon-ok-circle task-wait');
                                };
                            };
                        }, 'json').fail(function() {
                            console.log("error de conexion");
                        });

                        last_unit_move=null;
                    };
                    $("div[id^='popover']").remove();
                }
            }else if ($("#ch_contenido").attr('class')=='mode-delete') {
                var id_td = $(this).attr('id');
                id_td = id_td.split('_');
                showSpinner($("#ch_contenido"));
                $.post(server+'/checklist/sdtDeleteUnitTime','id='+_ut.attr('id')+'&task_id='+id_td[0], function(datos){
                    if (datos!='None') {
                        hideSpinner($("#ch_contenido"));
                        $('#tr_'+datos['task_id']).find('.percent').text(datos['task_percent']);
                        $("#"+datos['id']).remove();
                        if (datos['task_change_status']) {
                            $('#tr_'+datos['task_id']).find('.glyphicon-ok-circle').attr('class','glyphicon glyphicon-ok-circle task-wait');
                        };
                    };
                }, 'json').fail(function() {
                    console.log("error de conexion");
                });
                $("div[id^='popover']").remove();
            }else if ($("#ch_contenido").attr('class')=='mode-move') {
                if (!last_unit_move) {
                    _ut.attr('class','drag orange popover-unit-time dia');
                    last_unit_move = $(this);
                }else if (last_unit_move.attr('id')==$(this).attr('id')) {

                };
                $("div[id^='popover']").remove();
            }
        }
	});

	$('body').on('click', '.dummy-unit-time', function() {
		var class_u_t = $(this).attr('class');
		var follow_up = 0;
		if (class_u_t.indexOf('vacio')>0) {
			follow_up=1;
		}else if (class_u_t.indexOf('chuleado')>0) {
			follow_up=2;
		}else if (class_u_t.indexOf('nohizo')>0) {
			follow_up=3;
		}else if (class_u_t.indexOf('transferido')>0) {
			follow_up=4;
		}else if (class_u_t.indexOf('ciclo')>0) {
            follow_up=5;
        }
		var id_unit_time = $("div[aria-describedby^='popover']").attr('id');
        if (follow_up==5) {
            $("#sdt-repeat-task").modal('show');
        }else if (follow_up==4) {

			$("#sdt-dia").val(id_unit_time);
			$("#sdt-id_td").val($("#"+id_unit_time).parent().attr('id'));
			
		}else{
            showSpinner($("#ch_contenido"));
			$.post(server+'/checklist/sdtChangeFolloUp','id='+id_unit_time+"&follow="+follow_up, function(datos){
				if (datos!='None') {
                    hideSpinner($("#ch_contenido"));
					$('#'+id_unit_time).attr('class','drag orange popover-unit-time dia '+datos['color']);
					$('#'+id_unit_time).find('span').attr('class',datos['icon']+ ' circle');
					$('#tr_'+datos['task_id']).find('.percent').text(datos['task_percent']);
				};
			}, 'json').fail(function() {
			    console.log("error de conexion");
			});
		}
		$('#'+id_unit_time).popover('hide');
	});

	$('body').on('click', '.close-popover', function() {
		$(".popover-unit-time").popover('hide');
	});

	$("#sdt-guadar-traslado").click(function(){
		if ($("#sdt-fecha_traslado").val()) {
            showSpinner($("#ch_contenido"));
			$.post(server+'/checklist/sdtUnitTimeTransfer','id='+$("#sdt-dia").val()+"&newdate="+$("#sdt-fecha_traslado").val()+"&task="+$("#sdt-id_td").val(), function(datos){
				if (datos!='None') {
                    hideSpinner($("#ch_contenido"));
					var light = '';
					if ($('#'+datos['id']).attr('class').indexOf('light')>0) {
						light = 'light';
					};
					console.log('drag orange popover-unit-time dia '+datos['color']+light);

					$('#'+datos['id']).attr('class','drag orange popover-unit-time dia '+datos['color']+light);
					$('#'+datos['id']).find('span').attr('class',datos['icon']+ ' circle');

					if (dateinViewChecklist($("#sdt-fecha_traslado").val())) {
						var unit_time = '<div class="drag orange popover-unit-time '+datos['color_new']+'" id="'+datos['id_new']+'">'+
									'<div class="text-center">'+
										'<span class="'+datos['icon_new']+' circle"></span>'+
									'</div>'+
								'</div>';
						unit_time = $(unit_time);
						var column = diffDates($("#ch-back-range").val(),$("#sdt-fecha_traslado").val())+4;
						var task = datos['task'].split('_');
						var _id_td = task[0]+"_"+task[1]+"_"+column;
						console.log('id td '+_id_td);
						$("#"+_id_td).append(unit_time);
						agregarPopover(unit_time);
					};
				};
			}, 'json').fail(function() {
			    console.log("error de conexion");
			});
		};
	});
	
	$('#ch_contenido').on('click', "#btn_add_task", function(e){
		$("#sdt-new-task").modal('show');
	});
	$("#sdt-new-task-submit").click(function(){
		var name = $("#sdt-new-task-name").val();
		var description = $("#sdt-new-task-description").val();
        var fecha = $("#sdt-new-task-fecha").val();
		var priority = $("#sdt-new-task-priority").val();
		if (name.trim()!='') {
			$('#sdt-new-task-submit').attr("disabled", true);
			$.post(server+'/tasks/newTask','name='+name+'&description='+description+'&priority='+priority+'&date='+fecha, function(datos){
				if (datos!="None" && datos!="") {
					$('#sdt-new-task-submit').attr("disabled", false);
					$('#sdt-new-task').modal('hide');
					// cargarTareas(diaAgenda);
					//cargarTareas($('#ch-today').val());
					actualizarVistaCH();
				};
			}, 'json');
		};
	});
});


var agregarPopover = function (obj){
	// Agregar los popover con botones
	var btn1 = '<div class="drag orange dummy-unit-time vacio" style="float:left;margin-right: 5px;"><div class="text-center"><span class="glyphicon glyphicon-minus circle"></span></div></div>';
	var btn2 = '<div class="drag orange dummy-unit-time chuleado" style="float:left;margin-right: 5px;"><div class="text-center"><span class="glyphicon glyphicon-ok circle"></span></div></div>';
	var btn3 = '<div class="drag orange dummy-unit-time nohizo" style="float:left;margin-right: 5px;"><div class="text-center"><span class="glyphicon glyphicon-remove circle"></span></div></div>';
	var btn4 = '<div class="drag orange dummy-unit-time transferido" style="float:left;" data-toggle="modal" data-target="#sdt-transfer"><div class="text-center"><span class="glyphicon glyphicon-arrow-right circle"></span></div></div>';
    var btn5 = '<div class="drag orange dummy-unit-time ciclo" style="float:left;margin-left: 5px;"><div class="text-center"><span class="glyphicon glyphicon-repeat circle"></span></div></div>';


    var btns = btn1+'&nbsp;'+btn2+'&nbsp;'+btn3+'&nbsp;'+btn4 +'&nbsp;'+btn5;
    var clase = $(obj).attr('class');
    if(clase.indexOf("light")>-1){
        btns = btn4;
    }
    $(obj).popover({
	    placement: 'left',
	    html: 'true',
	    title : '<span class="text-info" style="width:190px"><strong>Seguimiento</strong></span><button type="button" id="close" class="close close-popover">&times;</button>',
	    content : btns
  	});
}

var parametrosCheclist = function () {
	var pathname = window.location.pathname;
	pathname = pathname.split('/');
	var parameters = '';
	var var_para = [];
	var_para[3]='team_id';
	var_para[4]='project_id';
	var_para[5]='package_id';
	var_para[6]='model';
	var_para[7]='task_id';
	for (var i = 3; i < pathname.length; i++) {
		parameters+='&'+var_para[i]+'='+pathname[i];
	};
    var tipo='&type=normal';
    var obj = $("#ch-type-delegate");
    if(obj){
        tipo = '&type='+obj.val();
    }
	return tipo+parameters;
}
var ajustarPopover = function(){
	 $("div[id^='popover']").css('width','158');
}
var actualizarVistaCH = function(){
	var fecha =  $("#ch-back-range").val();
	var fecha = operateDate(fecha,0);
    showSpinner($("#ch_contenido"));
	$.post(server+'/checklist/sdtChecklistView', 'date='+fecha+'&range='+$("#rangoVista").val() + parametrosCheclist(), function(datos){
        hideSpinner($("#ch_contenido"));
        $("#ch_contenido").html(datos);
		inicioCheckList();
		inicioFiltroCheckList();
		}).fail(function() {
   		console.log("error de conexion");
	});
} 
var inicioCheckList = function(){

	// darDiaNav($("#ch_btn_hoy"),-1);

	var dias = new Array();
	dias[0] = "Do";
	dias[1] = "Lu";
	dias[2] = "Ma";
	dias[3] = "Mi";
	dias[4] = "Ju";
	dias[5] = "Vi";
	dias[6] = "Sa";

	var diasCom = new Array();
	diasCom[0] = "Domingo";
	diasCom[1] = "Lunes";
	diasCom[2] = "Martes";
	diasCom[3] = "Miercoles";
	diasCom[4] = "Jueves";
	diasCom[5] = "Viernes";
	diasCom[6] = "Sabado";

	var meses = new Array();
	meses["01"] = "Enero";
	meses["02"] = "Febrero";
	meses["03"] = "Marzo";
	meses["04"] = "Abril";
	meses["05"] = "Mayo";
	meses["06"] = "Junio";
	meses["07"] = "Julio";
	meses["08"] = "Agosto";
	meses["09"] = "Septiembre";
	meses["10"] = "Octubre";
	meses["11"] = "Noviembre";
	meses["12"] = "Diciembre";

	$("#drag").css("display","absolute");
	//$("#tb0").width(anchoTabla);
	//$("#tbl").width(anchoTabla);
	$("#cargando").remove();
	
	$(".panel-body").removeClass();
	$("#contenido").removeClass();
	
	offsetG = $( "#tb0" ).offset();

    $("#tbl .popover-unit-time").each(function (index, element) {
        agregarPopover(element);
    });

	$('#ch_contenido').on('click', '.popover-unit-time', function(){

		if ($(this).attr('class').indexOf('transferido')>-1) {
			// No me interesan las unidads que ya se transfirieron
			last_popover=null;
			// Por eso no hay mas accion que cerrar los popovers
			$('.popover-unit-time').popover('hide');
		}else{
			if (last_popover) {
				if (last_popover.attr('id')!=$(this).attr('id')) {
					last_popover.popover('hide');
				}else{
					last_popover=null;
				}
			}
			last_popover = $(this);
			setTimeout('ajustarPopover();',50);
		}
	});
	
	//$("#tb").width($("#main_container").width()-10);
	$("#tb0").css( "zIndex", 2 );
	$(".fijar1").css( "zIndex", 1 );
	$(".fijar2").css( "zIndex", 1 );
	$(".fijar3").css( "zIndex", 1 );

	$('#bt-searchRM').tooltip('show');
	$("#bt-searchRM").tooltip({
		placement: 'bottom',
		template: '<div class="tooltip" style="width: 100px;background-color:#ef0d0a;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});

	var dayx=0;
	var objini=null;
	var cellini=null;
	var textdif="";
	var numCells=0;
	$("#tb0 .cdark_top").each(function (index, element) {
		var newTexto = NewDate_Month_Year(meses, dayx);
		
		var divc = $('<div>'+newTexto+'</div>');
		// codigo para combinar celdas
		$(element).append(divc);
		if (textdif!=newTexto) {
			textdif=newTexto;
			if (objini) {
				$(cellini).attr('colspan',''+parseInt(numCells)+'');
				// $(cellini).css('width',parseInt(numCells)*30);
				$(objini).attr("data-toggle","tooltip");
				$(objini).attr("title",$(objini).text());
				if (parseInt(numCells)==3) {
					$(objini).text($(objini).text().slice(0,6));
				}else if (parseInt(numCells)==2) {
					$(objini).text($(objini).text().slice(0,4));
				}else if (parseInt(numCells)==2) {
					$(objini).text($(objini).text().slice(0,2));
				}
				$(objini).tooltip({
					placement: 'bottom',
					template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
				});
				numCells=0;
			};
			objini=$(divc);
			cellini=$(element);
		}else{
			$(element).remove();
		}
		numCells+=1;
		dayx+=1;
	});
	$(cellini).attr('colspan',''+parseInt(numCells)+'');
	$(objini).attr("data-toggle","tooltip");
	$(objini).attr("title",$(objini).text());
	if (parseInt(numCells)==3) {
		$(objini).text($(objini).text().slice(0,6));
	}else if (parseInt(numCells)==2) {
		$(objini).text($(objini).text().slice(0,4));
	}else if (parseInt(numCells)==1) {
		$(objini).text($(objini).text().slice(0,2));
	}
	$(objini).tooltip({
		placement: 'bottom',
		template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});
			
	var dayx=0;
	var dayx2=parseInt($("#ch-nweek").val());
	var cellini=null;
	var numtr = $('#tbl >tbody >tr').length;
	$("#tb0 .cdark_bottom").each(function (index, element) {
		var divs = $('<div style="position: relative;"></div>');

		var opcion1= $('<option value="Todo" selected>Todo</option>');
		var opcion2= $('<option value="circle">Cualquier estado</option>');
		var opcion3= $('<option value="minus">Sin resolver</option>');
		var opcion4= $('<option value="ok">Checkado</option>');
		var opcion5= $('<option value="arrow-right">Transferido</option>');
		var opcion6= $('<option value="remove">No realizado</option>');
		var select = $('<select id="p_d_'+(dayx+4)+'" class="filtro_dia" style="width: 20px;height: 10px;">');
		
		$(select).append(opcion1);
		$(select).append(opcion2);
		$(select).append(opcion3);
		$(select).append(opcion4);
		$(select).append(opcion5);
		$(select).append(opcion6);
		$(divs).append(select);

		$(element).append(divs);

		var divc = $('<div class="conten_dia">'+NewDate_NumDay(dayx)+'</div>');
		$(divs).append(divc);

		$(divc).attr("data-toggle","tooltip");
		$(divc).attr("title",diasCom[dayx2] + "  " + NewDate_Complete(meses, dayx));
		$(divc).tooltip({
			placement: 'bottom',
			template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
		});

		$(select).attr("data-toggle","tooltip");
		$(select).attr("title","Filtrar por Estado del dia");
		$(select).tooltip({
			placement: 'bottom',
			template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
		});
		// Encontrar el lunes
		if (dayx2==1) {
			// buscamos la celda de la columna de abajo
			//var element_botton = $("#tbl").find(" tr:eq(0) > td:eq("+(3+dayx)+")");
			$("#tbl").find("tr").each(function (index2, element2) {
				var element_botton = $(element2).find('td:eq('+(3+dayx)+')');
				// Buscar si la celta contiene otro elemento
				var time_u = element_botton.find(".circle");
				if ($(time_u).length==0) {
					time_u = element_botton.find(".circlemas");
				}
				// esto con el fin de ajustar la altura
				if ($(time_u).length==0) {
					var div_down = $('<div class="beginweek" style="height:'+($(element2).height())+'px"></div>');

				}else{
					var div_down = $('<div class="beginweek" style="height:'+($(element2).height())+'px;top:-28px;"></div>');
				}
				var divr = $('<div style="position: relative;"></div>');
				$(divr).append(div_down);
				element_botton.append(divr);
			});
			
			// division para la tabla cabecera
			var div_top = $('<div class="beginweek" style="height:36px"></div>');
			$(divs).append(div_top);
		};

		dayx+=1;
		dayx2+=1;
		if (dayx2>6) {
			dayx2=0;
		};
	});
	// Funcionalidad ocultar y mostrar barra de lateral de Menu
	var posleftwrap=null;
	$("#btn_all_screen").attr("data-toggle","tooltip");
	$("#btn_all_screen").attr("title","Ocultar Menu de Navegacion");
	$("#btn_all_screen").tooltip({
		placement: 'bottom',
		template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	});


	// // Desplazar scroll al dia hoy
	// var leftPos = $('#tabla').scrollLeft(); 
 //    if (parseInt(ntoday)>23 && (parseInt(ntoday)-3)<=parseInt(ndays)) {
 //    	$("#tabla").animate({
	//         scrollLeft: leftPos + (parseInt(ntoday)-23)*30+36+250+10
	//     }, 800);
 //    }


 //    // actualizar los porcentajes de las filas periodicas.
	// $("#tbl").find("tr").each(function (index, element) {
	// 	var id_tr = $(element).attr("id");
	// 	id_tr = id_tr.split("_");
	// 	id_tr=id_tr[1];
	// 	var barrapross = $('#progressbar_'+id_tr);
	// 	var name_bar = $(barrapross).attr("name");
	// 	name_bar = name_bar.split("_");
	// 	var porcentaje=name_bar[1];
	// 	var spanVisita = $(element).find("span[class^='label label-warning']");

	// 	if ($(element).find(".redondo").length) {
	// 		var hijo = $(element).find("td[class^='only cligth2']");
	// 		var cn=0;
	// 		var cn2=0;
	// 		$(element).find(".orange").each(function (index2, element2) {
				
	// 			var hijo2 = $(element2).find(".circle");
	// 			if (!hijo2.length) {
	// 				hijo2 = $(element2).find(".circlemas");
	// 			};
	// 			if (!hijo2.length) {
	// 				hijo2 = $(element2).find(".circlemasmas");
	// 			};
	// 			if ($(hijo2).attr("class").indexOf(ico_later)==-1) {
	// 				cn+=1;
	// 				if ($(hijo2).attr("class").indexOf(ico_check)>-1) {
	// 					cn2+=1;
	// 				};
	// 				if (cn2<0) {
	// 					cn2=0;
	// 				};
	// 			};
				
	// 		});

	// 		if (cn===0) {
	// 			//hijo.text("0.00");
	// 		}else{
	// 			//hijo.text((cn2 / cn * 100).toFixed(2));
	// 			porcentaje=(cn2 / cn * 100).toFixed(2);
	// 		}
	// 	}
	// 	$(barrapross).text(porcentaje);
	// });


	
	// Si se encuentra aceptada la tarea se mostraran los objetivos
	$('#ch_contenido').on('click', ".task-wait", function(e){
	//$("span[id^='re_']").click(function(){
		var ide = $(this).attr("id");
		ide = ide.slice(3,ide.length);
		$("#ch-RespTareaId").val(ide);
		$('#myModalRespTarea').modal('show');
	});

    $('.task-wait').tooltip('show');
}
var operateDate = function (date, nday){
	var fecha = date.split('/');
	var fecha=new Date(fecha[2], fecha[1]-1, fecha[0]);
	var tiempo=fecha.getTime();
	var milisegundos=parseInt(nday*24*60*60*1000);
	var total=fecha.setTime(tiempo+milisegundos);
    var day=fecha.getDate();
    var month=fecha.getMonth()+1;
    var year=fecha.getFullYear();
    if (parseInt(day)<10) {
    	day="0"+day;
    };
    if (parseInt(month)<10) {
    	month="0"+month;
    };
    return day+"/"+month+"/"+year;
}
var dateinViewChecklist = function (date){
	console.log('Avanzar '+diffDates(date, $("#ch-next-range").val()));
	if (diffDates(date, $("#ch-next-range").val())>1) {
		return true;
	};
	return false;
}
var diffDates = function (date_start, date_end){
	var fecha1 = date_start.split('/');
	var date_start =new Date(fecha1[2], fecha1[1]-1, fecha1[0]);

	var fecha1 = date_end.split('/');
	var date_end =new Date(fecha1[2], fecha1[1]-1, fecha1[0]);

	var fechaResta= date_end.getTime()-date_start.getTime();

	fechaResta=(((fechaResta/1000)/60)/60)/24;

	return fechaResta;
}
var NewDate_Complete = function(meses, days){
    var fecha=new Date($("#ch-start-year").val(), $("#ch-start-month").val() -1, $("#ch-start-day").val());
    //Obtenemos los milisegundos desde media noche del 1/1/1970
    var tiempo=fecha.getTime();
    //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
    var milisegundos=parseInt(days*24*60*60*1000);
    //Modificamos la fecha actual
    var total=fecha.setTime(tiempo+milisegundos+1*60*60*1000);
    var day=fecha.getDate();
    var month=fecha.getMonth()+1;
    var year=fecha.getFullYear();
    if (parseInt(day)<10) {
    	day="0"+day;
    };
    if (parseInt(month)<10) {
    	month="0"+month;
    };
    return day+" de "+meses[month] +" del "+year;
}

var NewDate_NumDay = function(days){
    var fecha=new Date($("#ch-start-year").val(), $("#ch-start-month").val() - 1, $("#ch-start-day").val(),0,0,9);
    //Obtenemos los milisegundos desde media noche del 1/1/1970
    var tiempo=fecha.getTime();
    //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
    var milisegundos=parseInt(days*24*60*60*1000);
    //Modificamos la fecha actual
    var total=fecha.setTime(tiempo+milisegundos+1*60*60*1000);
    var day=fecha.getDate();
    if (parseInt(day)<10) {
    	day="0"+day;
    };
    return day;
}
var NewDate_Month_Year = function(meses,days){
    var fecha=new Date($("#ch-start-year").val(), $("#ch-start-month").val() - 1, $("#ch-start-day").val());
    //Obtenemos los milisegundos desde media noche del 1/1/1970
    var tiempo=fecha.getTime();
    //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
    var milisegundos=parseInt(days*24*60*60*1000);
    //Modificamos la fecha actual
    var total=fecha.setTime(tiempo+milisegundos+1*60*60*1000);
    var month=fecha.getMonth()+1;
    var year=fecha.getFullYear();
    if (parseInt(month)<10) {
    	month="0"+month;
    };
    return meses[month]+" "+year;
}

var getIdTask = function(tr){
	if (tr.attr("class")!="input-group") {
		var id_tr = $(tr).attr("id");
		id_tr = id_tr.split("_");
		return id_tr[1];
	}
}