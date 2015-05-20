
var calendar = $(document).ready(function() {

	usuarios_ele = [{"valor":"24","clase":"usuario-sistema","texto":"Temp"},{"valor":"1","clase":"usuario-sistema","texto":"Temp2"}];
	seguimiento_ele = [{"valor":2,"clase":"glyphicon glyphicon-ok","texto":""},{"valor":3,"clase":"glyphicon glyphicon-remove","texto":""},{"valor":4,"clase":"glyphicon glyphicon-arrow-right","texto":""}];
    prioridad_ele = [{"valor":1,"clase":"glyphicon glyphicon-exclamation-sign rojo","texto":"Alta"},{"valor":2,"clase":"glyphicon glyphicon-exclamation-sign amarillo","texto":"Media"},{"valor":3,"clase":"glyphicon glyphicon-exclamation-sign verde","texto":"Baja"},{"valor":4,"clase":"glyphicon glyphicon-exclamation-sign azul","texto":"Informativa"}];

    // page is now ready, initialize the calendar...
    $('#calendar').fullCalendar({
        // put your options and callbacks here
        header: {
			left: 'prev,next today',
			center: 'title'
		},
		defaultView: 'agendaDay',
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			// var title = prompt('Event Title:');
			// if (title) {
			// 	eventData = {
			// 		title: title,
			// 		start: start,
			// 		end: end
			// 	};
			// 	start =moment(start).format('YYYY/MM/DD HH:mm:ss');
			// 	end = moment(end).format('YYYY/MM/DD HH:mm:ss');
			// 	$.ajax({
			// 			url: server+'/sdt_live/htd_agregar_eventos',
			// 			data: 'title='+ title+'&start='+ start +'&end='+ end ,
			// 			type: "POST",
			// 			success: function(json) {
			// 				alert('OK');
			// 			}
			// 	});
			// 	$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
			// }
			// $('#calendar').fullCalendar('unselect');
		},

		editable: true,
		eventDrop: function(event, delta) {
		 // 	start =moment(event.start).format('YYYY/MM/DD HH:mm:ss');
			// end = moment(event.end).format('YYYY/MM/DD HH:mm:ss');
		 // 	$.ajax({
			// 	 	url: server+'/sdt_live/htd_modificar_eventos',
			// 	 	data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
			// 	 	type: "POST",
			// 	 	success: function(json) {
			// 	 		//alert("OK");
			//  		}
			//  	});
			},
		eventResize: function(event) {
			// start =moment(event.start).format('YYYY/MM/DD HH:mm:ss');
			// end = moment(event.end).format('YYYY/MM/DD HH:mm:ss');
			// $.ajax({
			//  		url: server+'/sdt_live/htd_modificar_eventos',
			// 	 	data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
			// 	 	type: "POST",
			// 		success: function(json) {
			// 		 	//alert("OK");
			// 		}
			// 	});
			},
		allDaySlot: false,
		events: server+'/htd/getEvents',
		droppable: true, // this allows things to be dropped onto the calendar
		drop: function(date, jsEvent, ui) {
			// start =moment(date).format('YYYY/MM/DD HH:mm:ss');
			// end = moment(date).add(2, 'hour').format('YYYY/MM/DD HH:mm:ss');

			// // start = date._a[0]+'/'+(date._a[1]+1)+'/'+date._a[2]+' '+date._a[3]+':'+date._a[4]+':'+date._a[5];
			// console.log(start + "  --  "+ end);
			// $(this).remove();
		},
		eventClick: function(calEvent, jsEvent, view) {
			// var _url = calEvent.url;
			// if (typeof _url == "undefined"){
		 //        // alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
		 //        // alert('View: ' + view.name);

		 //        // change the border color just for fun
		 //        // $(this).css('border-color', 'red');
		 //        _eventCalTemp = calEvent;
		 //        $("#tarea-titulo-sdt").val(calEvent.title);
			// 	$("#id-tarea").val(calEvent.id);
			// 	$("#tipo-evento").val(0);
			// 	$('#myModalTareaTitulo').modal('show');
			// }else{
			// 	 event.preventDefault();
			// }
	    }
    });

	$("#sdt-guadar-traslado").click(function(){
		if ($("#sdt-fecha_traslado").val()) {
			$.post(server+'/checklist/sdtUnitTimeTransfer','id='+$("#sdt-dia").val()+"&newdate="+$("#sdt-fecha_traslado").val()+"&task="+$("#sdt-id_td").val(), function(datos){
				if (datos!='None') {
                    var dat1 = $('#calendar').fullCalendar('getDate');
                    diaAgenda = dat1.utc().format('DD/MM/YYYY');
					var dia =  diaAgenda;
					cargarTareas(dia,1);

				};
			}, 'json').fail(function() {
			    console.log("error de conexion");
			});
		};
	});

	$('#htd_contenedor').on('click', ".gotodate", function(e){
		var clase = $(this).attr("class");
		clase =  clase.split(" ");
		var fe = clase[1].split("-");
        diaAgenda = parseInt(fe[2])+'/'+parseInt(fe[1])+'/'+parseInt(fe[0]);
        $('#htd_searchdate').val(diaAgenda);
        cargarTareas(diaAgenda);
        $('#calendar').fullCalendar( 'gotoDate', new Date(clase[1]));
	});

	$('#htd_contenedor').on('click', ".editar-tarea", function(e){
		var _id = $(this).attr('class');
		_id = _id.split(' ');
		_id = _id[1].split('_');
		_id = _id[1];
		$.post(server+'/tasks/getTask','id='+_id, function(datos){
			if (datos!="None" && datos!="") {
				$('#sdt-actual-task-id').val(datos.id);
				$('#sdt-actual-task-name').val(datos.name);
				$('#sdt-actual-task-description').val(datos.description);
				$('#sdt-actual-task-priority').val(datos.priority_id);
				$('#sdt-actual-task').modal('show');
			};
		}, 'json');
	});
	$("#sdt-actual-task-submit").click(function(){
		var _id = $("#sdt-actual-task-id").val();
		var name = $("#sdt-actual-task-name").val();
		var description = $("#sdt-actual-task-description").val();
		var priority = $("#sdt-actual-task-priority").val();
		if (name.trim()!='') {
			$('#sdt-actual-task-submit').attr("disabled", true);
			$.post(server+'/tasks/updateTask','id='+_id+'&name='+name+'&description='+description+'&priority='+priority, function(datos){
				if (datos!="None" && datos!="") {
					$('#sdt-actual-task-submit').attr("disabled", false);
					$('#sdt-actual-task').modal('hide');
					cargarTareas(diaAgenda,3);
				};
			}, 'json');
		};
	});

	// Ver registro
	$('#htd_contenedor').on('click', ".rm-tarea", function(e){
		var _id = $(this).attr('class');
		_id = _id.split(' ');
		_id = _id[1].split('_');
		_id = _id[1];
		console.log(_id);
		$.post(server+'/rmregistries/getRegistry','id='+_id, function(datos){
			if (datos!="None" && datos!="") {
				$("#contendor-rm-htd-temp").html('');
				$("#contendor-rm-htd-temp").html(datos);
				$("#sdt-rm-task").modal('show');
			};
		});
	});

    $("#htd_searchdate").change(function(){
        var date_sel = ""+$("#htd_searchdate").val()+"";
        cargarTareas(date_sel,4);
        var nt = date_sel.split("/");
        nt = nt[2]+'-'+nt[1]+'-'+nt[0]+' 01:01:01';
        $('#calendar').fullCalendar( 'gotoDate', new Date(nt));
    });

    $("#htd_searchdate").click(function(){
        fechaHTDOld=null;
    });

    $("#htd-today-go").click(function(){
        $("#htd_searchdate").val($("#htd-today").val());
        var date_sel = ""+$("#htd_searchdate").val()+"";
        cargarTareas(date_sel,10);
        var nt = date_sel.split("/");
        nt = nt[2]+'-'+nt[1]+'-'+nt[0]+' 01:01:01';
        $('#calendar').fullCalendar( 'gotoDate', new Date(nt));
    });

    cargarTareas($("#htd_searchdate").val());
    $("#sdt-htd-date-string").text(dateToString($("#htd_searchdate").val()));

    timeHTD=setInterval(showDaysHTD, 300);
});

var renderizarTareasHTD = function(){
	/* initialize the external events
		-----------------------------------------------------------------*/
	$('#hoja_diario .fc-event').each(function() {

		// store data so the calendar knows to render an event upon drop
		$(this).data('event', {
			title: $.trim($(this).text()), // use the element's text as the event title
			stick: true // maintain when user navigates (see docs on the renderEvent method)
		});

		// make the event draggable using jQuery UI
		$(this).draggable({
			zIndex: 10000,
			revert: true,      // will cause the event to go back to its
			revertDuration: 0  //  original position after the drag
		});
	});
}
var modificarEstadoCH = function(id_ele,indi,datos){
	var light='';
	if (dia<ntoday) {
		light="light";
	};
	if (datos!="None") {
		$("#"+id_ele).attr("id",datos);
		id_ele=datos;
	};
	var clact = $("#"+id_ele).attr("class");
	clact = clact.split(" ");
	if (indi==0) {
		$("#"+id_ele).find(".circle").attr("class",ico_null + " circle");
		$("#"+id_ele).attr("class",clact[0] + " orange dia " +"vacio"+light);
	};
	if (indi==1) {
		$("#"+id_ele).find(".circle").attr("class",ico_check + " circle");
		$("#"+id_ele).attr("class",clact[0] + " orange dia " +"chuleado"+light);
	};
	if (indi==2) {
		$("#"+id_ele).find(".circle").attr("class",ico_later + " circle");
		$("#"+id_ele).attr("class",clact[0] + " orange dia " +"transferido"+light);
	};
	if (indi==3) {
		$("#"+id_ele).find(".circle").attr("class",ico_remove + " circle");
		$("#"+id_ele).attr("class",clact[0] + " orange dia " +"nohizo"+light);
	};
}
var reiniciarEspacio = function () {
	$("#sortable").html("");
	var celda = $("#sortable");
	var li_array = [];
	for (var i = 0; i < 48; i++) {
		li_array[i] = $('<li class="newTask '+i+'">' +
						'<div class="text-center">' +
							'<span class="glyphicon glyphicon-plus" style="font-size: 15px;display:block;"></span>' +
						'</div>' +
					'</li>');
		celda.append(li_array[i]);
	};
}
var buscarOpciones =  function(li){
	var objs = [];
	var ul_ = $(li).parent();
    objs[0] = ul_.find(".seleccionado");
    objs[1] = $(li).find('span');
    objs[2] = ul_.parent();
    objs[2] = objs[2].find(".glyphicon:eq(0)");
    return objs;
}

var construirlistas =  function(atributo){
	// Si no es el usuario responsable de la tarea entonces no enlistar otras personas
	var list_temp_user = [];
	var list_temp_follow = [];
	var list_temp_prio = [];
	if (atributo.task_user_id !=atributo.user_id || atributo.status_tarea!=1) {
		for (var i = 0; i < usuarios_ele[atributo.team_id].length; i++) {
			if (usuarios_ele[atributo.team_id][i].valor==atributo.task_user_id) {
				var obj = new Object;
				obj.valor = usuarios_ele[atributo.team_id][i].valor;
				obj.clase = 'glyphicon glyphicon-user';
				obj.texto = usuarios_ele[atributo.team_id][i].texto;
				list_temp_user.push(obj);
				i=usuarios_ele[atributo.team_id].length;
			};
		};
		for (var i = 0; i < seguimiento_ele.length; i++) {
			if (seguimiento_ele[i].valor==atributo.estado) {
				var obj = new Object;
				obj.valor = seguimiento_ele[i].valor;
				obj.clase = seguimiento_ele[i].clase;
				obj.texto = seguimiento_ele[i].texto;
				list_temp_follow.push(obj);
				i=list_temp_follow.length;
			}
		};
		for (var i = 0; i < seguimiento_ele.length; i++) {
			if (prioridad_ele[i].valor==atributo.prioridad) {
				var obj = new Object;
				obj.valor = prioridad_ele[i].valor;
				obj.clase = prioridad_ele[i].clase;
				obj.texto = prioridad_ele[i].texto;
				list_temp_prio.push(obj);
				i=list_temp_prio.length;
			}
		};
	}else{
		list_temp_user = usuarios_ele[atributo.team_id];
		list_temp_follow = seguimiento_ele;
		list_temp_prio = prioridad_ele;
	}


    clase_follow_default="glyphicon glyphicon-minus";
	if (atributo.estado==4) {
        clase_follow_default = "glyphicon glyphicon-arrow-right";
		list_temp_follow = [];
	};


	var cont_menu = $('<div style="position:absolute;top:10px;right:0px;cursor: pointer;"></div>');
	$(cont_menu).drop_down_naan({
		id:atributo.id_dia,
		task_id:atributo.id_tarea,
		clase_cabeza:"head-drop-down",
		clase_elemento:"element-drop-down",
		listas: [
			{
				valor_x_defecto:atributo.responsable,
				clase_x_defecto:"glyphicon glyphicon-minus",
				clase_x_texto:false,
				elementos:list_temp_user,
				elemento_click: function(valor,id){
					$.post(server+'/htd/delegate','unit_id='+id+'&person='+valor, function(datos){
					 	//modificarEstadoCH(id,valor,datos);
                        var dat1 = $('#calendar').fullCalendar('getDate');
                        diaAgenda = dat1.utc().format('DD/MM/YYYY');
						var dia =  diaAgenda;
						cargarTareas(dia,5);
					}, 'json').fail(function() {
					});
					return true;
				}
			},
			{
				valor_x_defecto:atributo.estado,
				clase_x_defecto:clase_follow_default,
				clase_x_texto:false,
				elementos:list_temp_follow,
				elemento_click: function(valor,id,task_id,changeId){
					if (valor==4) {
						$("#sdt-dia").val(id);
						$("#fecha_trans").val(diaAgenda);
						$("#sdt-fecha_traslado").val(diaAgenda);
						$("#sdt-id_td").val(task_id);
						$("#sdt-transfer").modal('show');
						return false;
					}else{
                        var nextday = null;
                        var uni_basic = uniBasic(id);
                        if(uni_basic){
                            fechaIni = dateNewStandard(diaAgenda);
                            nextday = getNextDay(unitTimes[uni_basic].opt,dateFormantStandar(diaAgenda));
                            if(nextday){
                                nextday = moment.utc(nextday).format('YYYY-MM-DD');
                            }
                        }
						// modificarEstadoCH(id,valor,id+1);
						$.post(server+'/htd/UpdateFollowUp','unit_id='+id+'&follow='+valor+'&nextday='+nextday+'&coldate='+diaAgenda, function(datos){
					 	   	//modificarEstadoCH(id,valor,datos);
                            // changeId(2222222);
                            if(datos!=id){
                                var uni_basic = uniBasic(id);
                                changeInds(uni_basic,id,datos);
                                changeId(datos);
                            }
					 	}, 'json').fail(function() {
						});
						return true;
					}
				}
			},
			{
				valor_x_defecto:atributo.prioridad,
				clase_x_defecto:"glyphicon glyphicon-minus",
				clase_x_texto:false,
				elementos:list_temp_prio,
				elemento_click: function(valor,id){
					$.post(server+'/htd/aupdatePriority','unit_id='+id+'&priority_id='+valor, function(datos){
				 		
				 	}, 'json').fail(function() {
					});
					return true;
				}
			}
		]
	});;
	return cont_menu;
}
var cargarTareas = function(fecha,i){
    // console.log('Aqui '+ i);
	// Aqui desaparecer las tareas
    showSpinner($("#htd_contenedor"));
	$.post(server+'/htd/getTasks','day='+fecha, function(datos){
		if (datos!="None") {
            hideSpinner($("#htd_contenedor"));
            $("#sdt-htd-date-string").text(dateToString(fecha));
            $("#htd_contenedor").html('');
			// Sacar usuario por grupo usuarios_ele = [{"valor":"24","clase":"usuario-sistema","texto":"Temp"}];
			usuarios_ele = [];
			usuarios_ele[0] = new Array();
			var _usu = [];
			for (var i = 0; i < datos.teams.length; i++) {
				if (!(datos.teams[i].usersTeams.team_id in usuarios_ele)) {
					usuarios_ele[datos.teams[i].usersTeams.team_id] = new Array();
				};
				var obj = new Object;
				obj.valor = datos.teams[i].usersTeams.user_id;
				obj.clase = 'glyphicon glyphicon-user';
				obj.texto = datos.teams[i].username;
				usuarios_ele[datos.teams[i].usersTeams.team_id].push(obj);

				if (!(datos.teams[i].usersTeams.user_id in _usu)){
					_usu[datos.teams[i].usersTeams.user_id] = 0;
					usuarios_ele[0].push(obj);
				}
				
			};
            task_htd = new Array();
			agregarTarea(datos.tasks,fecha,datos.user,datos.labels);
            agregarTareaRepetitiva(datos.tasksrepeats,fecha,datos.user);
            var div_c = $('<a class="list-group-item list-group-item-success"><div class="text-center add-task-htd" style="cursor: pointer;">Agregar Tarea</div></a>');
            $("#htd_contenedor").append(div_c);
		};
	}, 'json').fail(function() {
	    console.log("error de conexion");
	});
}
var agregarTareaRepetitiva = function (datos,fecha,user) {
    for (var i = 0; i < datos.length; i++) {
        if(!task_htd[datos[i].tasks.id]){
            task_htd[datos[i].tasks.id]=true;
            // verificar si la unidad recurrente es del "este" dia
            var id_temp         =   datos[i].tasks.id+'_'+datos[i].tasksRepeats.unid_time_id+'_'+Math.floor((Math.random() * 1000000) + 1);
            var result = getHTDrepeatUltimate(datos[i],id_temp,dateNewStandard(diaAgenda));
            if(result.length>0){
                result = result[result.length-1];
                var day_y = moment.utc(result).format('DD/MM/YYYY');
                if(diaAgenda==day_y){
                    $("#htd_contenedor").append(createTaskHTD(datos[i],fecha,user));
                }
            }
        }
    }
}

var createTaskHTD = function(dato,fecha,user){
    tareas = new Object();
    tareas.id_tarea = dato.tasks.id;
    tareas.status_tarea = dato.tasks.status;
    if(dato.unidTimes){
        tareas.id_dia = dato.unidTimes.id;
        tareas.estado = dato.unidTimes.follow_up;
        tareas.prioridad = dato.unidTimes.priority_id;
        tareas.fecha_ant = dato.unidTimes.back_day;
        tareas.fecha_pos = dato.unidTimes.next_day
    }else{
        var id_temp         =   dato.tasks.id+'_'+dato.tasksRepeats.unid_time_id+'_'+Math.floor((Math.random() * 1000000) + 1);
        if(!unitTimes[dato.tasksRepeats.unid_time_id]){
            unitTimes[dato.tasksRepeats.unid_time_id]       = new Object();
            unitTimes[dato.tasksRepeats.unid_time_id].opt   = dato.tasksRepeats;
            unitTimes[dato.tasksRepeats.unid_time_id].ut    = [];
        }
        unitTimes[dato.tasksRepeats.unid_time_id].ut.push(id_temp);
        timesUnit[id_temp] = dato.tasksRepeats.unid_time_id;
        tareas.id_dia = id_temp;
        tareas.estado = 1;
        tareas.prioridad = dato.tasks.priority_id;
        tareas.fecha_ant = '0000-00-00';
        tareas.fecha_pos = '0000-00-00';
    }

    // tareas.pos = i;
    tareas.task_user_id = dato.tasks.user_id;
    tareas.user_id = user;
    if (dato.projects.team_id) {
        tareas.team_id = dato.projects.team_id;
    }else if (dato.packages.team_id) {
        tareas.team_id = dato.packages.team_id;
    }else{
        tareas.team_id = 0;
    }

    tareas.nombre_tarea = dato.tasks.name;
    tareas.sello = dato.rmRegistriesTasks.rm_registry_id; // La identificacion que viene del RM
    // Agregar el boton de editar
    var btn_edit = '';
    if (dato.tasks.project_id==0 && dato.tasks.package_id==0) {
        btn_edit = '<button type="button" class="editar-tarea task-id-htd_'+tareas.id_tarea+' btn btn-default btn-xs">E</button>';
    };
    var btn_rm = '';
    var style_rm = '';
    if (tareas.sello != null){
        btn_edit = '<button type="button" class="rm-tarea task-id-rm_'+tareas.sello+' btn btn-default btn-xs">R</button>';

        // agregar estilo dependiendo de la etiqueta
        if(dato.rm_label_id>0){
            var ia = dato.rm_label_id;
            if (labelsarr.hasOwnProperty(ia)) {
                var obj = labelsarr[ia][0];
                style_rm = 'style="color:'+obj.rmLabels.color+'; background-color:'+obj.rmLabels.b_color+';font-size:'+obj.rmSizes.size+';font-family:'+obj.rmFonts.name+'"';
            }
        }
    }

    if (tareas.prioridad==0) {
        tareas.prioridad = dato.tasks.priority_id;
    };

    tareas.responsable = dato.tasks.user_id;


    var btn_back = '';
    if (tareas.fecha_ant!="0000-00-00") {
        // btn_back = '<button type="button" class="gotodate '+tareas[i].fecha_ant+'"><span style="top:-1px;" class="glyphicon glyphicon-arrow-left"></span></button>';
        btn_back = '<button type="button" class="gotodate '+tareas.fecha_ant+' btn btn-default btn-xs"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></button>';
    }
    var btn_front = '';
    if (tareas.fecha_pos!="0000-00-00") {
        // btn_front = '<button type="button" class="gotodate '+tareas[i].fecha_pos+'"><span style="top:-1px;" class="glyphicon glyphicon-arrow-right"></span></button>';
        btn_front = '<button type="button" class="gotodate '+tareas.fecha_pos+' btn btn-default btn-xs"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></button>';
    }

    var btn_info = '<span class="glyphicon glyphicon-info-sign task-info" id="info_'+tareas.id_tarea+'"></span>';
    var div_c = $('<a class="list-group-item" '+style_rm+'><div id="id-task-htd_'+tareas.id_tarea+'">'+ btn_info + btn_back + btn_front + btn_rm + btn_edit +' '+ tareas.nombre_tarea +'</div></a>');

    div_c.append(construirlistas(tareas));
    return div_c;
}
var agregarTarea = function (datos,fecha,user,labels) {
    // crear labels
    labelsarr  = new Array();
    for (var i = 0; i < labels.length; i++) {
        labelsarr[labels[i].rmLabels.id]=[{
            'rmFonts': labels[i].rmFonts,
            'rmLabels': labels[i].rmLabels,
            'rmSizes': labels[i].rmSizes
        }];
    }
	$("#htd_contenedor").html('');
	// Reinicio el arreglo que contiene las tareas en htd
	tareas = null;
	tareas  = new Array();
	for (var i = 0; i < datos.length; i++) {
        task_htd[datos[i].tasks.id]=true;
        //div_c.append(listas);
		$("#htd_contenedor").append(createTaskHTD(datos[i],fecha,user));
	}

}
var formatoDia = function(codigo){
	var date = new Date(codigo);

	tiempo=date.getTime();
    milisegundos=parseInt(AjusteDia*24*60*60*1000);
    total=date.setTime(tiempo+milisegundos);
    AjusteDia=0.5;
	
	
	var dia = ""+date.getUTCDate()+"";
	var mes = ""+(date.getMonth()+1)+"";

	if (dia.length==1) {dia="0"+dia};
	if (mes.length==1) {mes="0"+mes};
	return dia+"/"+mes+"/"+date.getFullYear();
}

var obtenerDia = function(diaVista,fila){
	diaAgenda = formatoDia(diaVista);
	var dia =  diaAgenda;
	//cargarTareas(dia, 6);
}


var seleccionarDelegado = function (obj_li) {
	var spa = buscarOpciones($(obj_li));
	if (spa[0].length) {
		spa[0].removeClass( "seleccionado" );
	};
	if (spa[1].length) {
		spa[1].addClass( "seleccionado" );
	};
	if (spa[2].length) {
		spa[2].addClass( "glyphicon-user" );
		spa[2].removeClass("glyphicon-minus");
	};
}

var showDaysHTD = function(){
    var mes = $(".ui-datepicker-month").val();
    if(!(typeof mes == "undefined")){
        mes = parseInt(mes)+1;
        if (mes<10) {
            mes="0"+mes
        };
        var anio=$(".ui-datepicker-year").val();
        var fecha = "01/"+mes+"/"+anio;
        if (fechaHTDOld!=fecha) {
            $.post(server+'/htd/get_days','date='+fecha, function(datos){
                $("#ui-datepicker-div td").each(function(index, elemento){
                    hijo = $(elemento).find( "a" );
                    dia=hijo.text().trim();
                    if (dia!="") {
                        if (dia<10) {
                            dia="0"+dia
                        };
                        if (typeof datos.days[dia] != "undefined"){
                            hijo.css("color","blue");
                            if (datos.days[dia]) {
                                hijo.css("color","#20ba13");
                            };
                        };
                    };
                });
            },'json').fail(function() {
                console.log("error de conexion");
                errorConexcion();
            });
            fechaHTDOld=fecha;
        };
    }
}
var uniBasic = function(id_unit_time){
    var uni_basic = 0;
    if(unitTimes[id_unit_time]){
        uni_basic = id_unit_time;
    }else if(timesUnit[id_unit_time]){
        uni_basic = timesUnit[id_unit_time];
    }
    return uni_basic;
}
var changeInds = function(uni_basic,old_Id,newId){
    for(var j=0;j<(unitTimes[uni_basic].ut.length);j++){
        if(unitTimes[uni_basic].ut[j]==old_Id){
            unitTimes[uni_basic].ut[j]=newId;
            timesUnit[newId] = timesUnit[old_Id];
            j=unitTimes[uni_basic].ut.length;
        }
    }
}