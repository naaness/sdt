/**
 * Created by nesto_000 on 11/04/15.
 */

/* enable strict mode */
"use strict";
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
            var task_id = $("#ch-RespTareaId").val();
            var status = $("#ch-respuesta").val();
            if(status==3){
                // remover la clase
                $("#tr_"+task_id).fadeOut();
            }else{
                // quitar clase task-wait
                $("#re_"+task_id).removeClass("task-wait");
                $("#re_"+task_id).addClass(state_task[status]);
                // Agregar la clase y popover a las unidades de tiempo de las vistas de esta tarea
                $("#tr_"+task_id+" .orange").each(function (index, element) {
                    $(element).addClass('popover-unit-time');
                    agregarPopover(element);
                });
            }
            $('#myModalRespTarea').modal('hide');
        })
        .fail(function() {
            console.log("error de conexion");
            $('#myModalRespTarea').modal('hide');
        });
    });

//    showSpinner($("#ch_contenido"));
//	$.post(server+'/checklist/sdtChecklistView', 'range=month' + parametrosCheclist(), function(datos){
//        hideSpinner($("#ch_contenido"));
//		$("#ch_contenido").html(datos);
//		inicioCheckList();
//		inicioFiltroCheckList();
//		}).fail(function() {
//   		console.log("error de conexion");
//	});

    $('body').on('click', '.dummy-unit-time', function() {
        var class_u_t = $(this).attr('class');
        var follow_up = 0;
        if (class_u_t.indexOf(segui.class_u.future[1])>0) {
            follow_up=1;
        }else if (class_u_t.indexOf(segui.class_u.future[2])>0) {
            follow_up=2;
        }else if (class_u_t.indexOf(segui.class_u.future[3])>0) {
            follow_up=3;
        }else if (class_u_t.indexOf(segui.class_u.future[4])>0) {
            follow_up=4;
        }else if (class_u_t.indexOf('ciclo')>0) {
            follow_up=5;
        }
        var id_unit_time = $("div[aria-describedby^='popover']").attr('id');
        if (follow_up==5) {
            createHtmlTaskRepat($("#sdt-repeat-task-html"));
            $("#sdt-repeat-dia").val(id_unit_time);
            $("#sdt-repeat-id_td").val($("#"+id_unit_time).parent().attr('id'));
            // verificar si la configuracion ya existe para esta unidad de tiempo addRepeat
            $.post(server+'/checklist/getRepeatConf','id='+id_unit_time, function(datos){

                if(datos!="None"){
                    giveConfig(datos);
                }else{
                    $("#op5").val(fechaCol);
                    configRepeatDefault();
                }
                $("#sdt-repeat-task").modal('show');
            }, 'json').fail(function() {
                console.log("error de conexion");
            });

        }else if (follow_up==4) {
            $("#sdt-dia").val(id_unit_time);
            $("#sdt-id_td").val($("#"+id_unit_time).parent().attr('id'));
            // si la fecha de la columna es meno que hoy  entonces dar la fecha de hoy
            $("#sdt-fecha_traslado").val(fechaCol);
            if(moment.utc(fechaTod).diff(moment.utc(dateNewStandard(fechaCol)), 'days')>=0){
                $("#sdt-fecha_traslado").val(moment.utc(fechaTod).format('DD/MM/YYYY'));
            }
            $("#sdt-danger-tranfer").css("display","none");
            $("#sdt-success-tranfer").css("display","none");
            $("#sdt-guadar-traslado").prop("disabled", false);
        }else{
            showSpinner($("#main_container"));
            // verificar si depende de una tarea repetitiva
            var next_day = null;

            var uni_basic = uniBasic(id_unit_time);
            if(uni_basic){
                // crear un next day
                next_day = getNextDay(unitTimes[uni_basic].opt,fechaCol);
                if(next_day){
                    next_day = moment.utc(next_day).format('YYYY-MM-DD');
                }
            }
            var coldate = moment.utc(dateNewStandard(fechaCol)).format('YYYY-MM-DD');

            var tr_id = $("#"+id_unit_time).parent().parent();

            $.post(server+'/checklist/sdtChangeFolloUp','id='+id_unit_time+"&follow="+follow_up+"&nextday="+next_day+"&coldate="+coldate+addPercent($(tr_id).attr("id")), function(datos){
                if (datos!='None') {
                    hideSpinner($("#main_container"));
                    $('#'+id_unit_time).attr('class','drag orange popover-unit-time dia '+datos['color']);
                    $('#'+id_unit_time).find('span').attr('class',datos['icon']+ ' circle');
                    $('#tr_'+datos['task_id']).find('.percent').text(datos['task_percent']);

                    if(datos.ut_next!=null){
                        var colT = moment.utc(dateNewStandard(datos.ut_next.start_day)).diff(moment.utc(fechaIni), 'days')+5;
                        var rowT = findNRowTable($("#tr_"+datos.ut_next.task_id));
                        var _ut = $("#"+datos.ut_next.task_id+'_'+rowT+'_'+colT).find(".orange");
                        if (_ut.length!=0) {
                            _ut.addClass(segui.class_u.future[1]);
                            var id_t = _ut.attr("id");
                            var uni_basic = uniBasic(id_unit_time);
                            changeInds(uni_basic,id_t,datos.ut_next.id);
                            _ut.attr("id",datos.ut_next.id);
                        }
                    }
                    if(datos.old_id!=datos.unid_s){
                        var uni_basic = uniBasic(datos.old_id);
                        changeInds(uni_basic,datos.old_id,datos.unid_s);
                        $("#"+datos.old_id).attr("id",datos.unid_s);
                    }
                    percentRow(datos['task_id']);
                };
            }, 'json').fail(function() {
                console.log("error de conexion");
            });
        }
        $('#'+id_unit_time).popover('hide');
    });

    // si se actualiza por una fecha de traslado inferior a la de hoy se deja la de hoy
    $("#sdt-fecha_traslado").change(function(){
        var actualDay = $("#sdt-fecha_traslado").val();
        if(moment.utc(fechaTod).diff(moment.utc(dateNewStandard(actualDay)), 'days')>0){
            $("#sdt-fecha_traslado").val(moment.utc(fechaTod).format('DD/MM/YYYY'));
            $("#sdt-success-tranfer").css("display","none");
            $("#sdt-danger-tranfer").css("display","block");
            $("#sdt-guadar-traslado").prop("disabled", true);
        }else{
            $("#sdt-success-tranfer").css("display","block");
            $("#sdt-danger-tranfer").css("display","none");
            $("#sdt-guadar-traslado").prop("disabled", false);
        }
    })
    $("#sdt-guadar-traslado").click(function(){
        if ($("#sdt-fecha_traslado").val()) {
            showSpinner($("#main_container"));
            $.post(server+'/checklist/sdtUnitTimeTransfer','id='+$("#sdt-dia").val()+"&newdate="+$("#sdt-fecha_traslado").val()+"&task="+$("#sdt-id_td").val()+addPercent($("#sdt-id_td").val()), function(datos){
                if (datos!='None') {
                    hideSpinner($("#main_container"));
                    var light = '';
                    if ($('#'+datos['id']).attr('class').indexOf('light')>0) {
                        light = 'light';
                    };

                    $('#'+datos['id']).attr('class','drag orange popover-unit-time dia '+datos['color']+light);
                    $('#'+datos['id']).find('span').attr('class',datos['icon']+ ' circle');

                    if (dateinViewChecklist(dateNew($("#sdt-fecha_traslado").val()))) {
                        var unit_time = '<div class="drag orange popover-unit-time '+datos['color_new']+'" id="'+datos['id_new']+'">'+
                            '<div class="text-center">'+
                            '<span class="'+datos['icon_new']+' circle"></span>'+
                            '</div>'+
                            '</div>';
                        unit_time = $(unit_time);
                        var column = diffDates(fechaIni,dateNew($("#sdt-fecha_traslado").val()))+5;
                        var task = datos['task'].split('_');
                        var _id_td = task[0]+"_"+task[1]+"_"+column;
                        $("#"+_id_td).append(unit_time);
                        agregarPopover(unit_time);
                    };
                };
            }, 'json').fail(function() {
                console.log("error de conexion");
            });
        };
    });
});

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
        btns = btn3+'&nbsp;'+btn4;
    }
    if(chEdit){
        btns = btn5;
    }
    $(obj).popover({
        placement: 'left',
        html: 'true',
        width:'168',
        title : '<span class="text-info" style="width:210px"><strong>Seguimiento</strong></span><button type="button" class="close close-popover">&times;</button>',
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
    return '&range='+$("#rangoVista").val()+parameters;
}
var ajustarPopover = function(){
    var ancho = $("div[class='popover fade left in']").css('width');
    if('118px'==ancho){
        $("div[id^='popover']").css('width','168');
    }
}
var inicioCheckList = function(){
//
    $(".panel-body").removeClass();
    $("#contenido").removeClass();

    $('#ch_content').on('click', '.close-popover', function(){
        $(".popover-unit-time").popover('hide');
    });

    $('#ch_content').on('click', '.popover-unit-time', function(){

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


//    $('#bt-searchRM').tooltip('show');
//    $("#bt-searchRM").tooltip({
//        placement: 'bottom',
//        template: '<div class="tooltip" style="width: 100px;background-color:#ef0d0a;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
//    });

    // Funcionalidad ocultar y mostrar barra de lateral de Menu
    var posleftwrap=null;
    $("#btn_all_screen").attr("data-toggle","tooltip");
    $("#btn_all_screen").attr("title","Ocultar Menu de Navegacion");
    $("#btn_all_screen").tooltip({
        placement: 'bottom',
        template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
    });


    // Desplazar scroll al dia hoy
    var leftPos = 0;
    if (parseInt(ntoday)>12 && ndiff>7 && ntoday<=ndiff) {
        $("#tabla").animate({
            scrollLeft: leftPos + (parseInt(ntoday)-12)*30+36+250+10
        }, 800);
    }else{
        $("#tabla").animate({
            scrollLeft: 0
        }, 800);
    }
    // Si se encuentra aceptada la tarea se mostraran los objetivos
    $('#ch_content').on('click', ".task-wait", function(e){
        //$("span[id^='re_']").click(function(){
        var ide = $(this).attr("id");
        if(ide){
            ide = ide.slice(3,ide.length);
            $("#ch-RespTareaId").val(ide);
            $('#myModalRespTarea').modal('show');
        }
    });

    $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
    });
    $( "#datepicker" ).datepicker({ minDate: "-1D", maxDate: "+1M +10D" });

    $('#ch_content').on('click', '#ch-mode-1', function(){
        $("#ch_content").removeClass('mode-add');
        $("#ch_content").removeClass('mode-delete');
        $("#ch_content").removeClass('mode-move');
        $("div[id^='popover']").remove();
        $(".no-editable-tr").removeClass('noedittr');
    });
    $('#ch_content').on('click', '#ch-mode-2', function(){
        $("#ch_content").addClass('mode-add');
        $("#ch_content").removeClass('mode-delete');
        $("#ch_content").removeClass('mode-move');

        $("div[id^='popover']").remove();
        $(".no-editable-tr").addClass('noedittr');
    });
    $('#ch_content').on('click', '#ch-mode-3', function(){
        $("#ch_content").removeClass('mode-add');
        $("#ch_content").addClass('mode-delete');
        $("#ch_content").removeClass('mode-move');
        $("div[id^='popover']").remove();
        $(".no-editable-tr").addClass('noedittr');
    });
    $('#ch_content').on('click', '#ch-mode-4', function(){
        $("#ch_content").removeClass('mode-add');
        $("#ch_content").removeClass('mode-delete');
        $("#ch_content").addClass('mode-move');
        $("div[id^='popover']").remove();
        $(".no-editable-tr").addClass('noedittr');
    });
    var cl =$('#ch_content').attr('class');
    if(cl){
        if(cl.indexOf('mode')>-1){
            $(".no-editable-tr").addClass('noedittr');
        }
    }

    $('#ch_content').on('click', '#tbl .editable-yes .orange', function(){
        var _ut = $(this);
        var parent = $(this).parent();
        var id_td = parent.attr('id');
        id_td = id_td.split('_');
        var day_num = id_td[2]-5;
        fechaCol = moment.utc(fechaIni).add('days', day_num).format('DD/MM/YYYY');
        var class_ch = $("#ch_content").attr('class');

        if ( class_ch.indexOf('mode-delete')>-1) {
            showSpinner($("#main_container"));
            $.post(server+'/checklist/sdtDeleteUnitTime','id='+_ut.attr('id')+'&task_id='+id_td[0]+addPercent(id_td[0]), function(datos){
                hideSpinner($("#main_container"));
                if (datos!='None') {
                    if(datos.task_repeat){
                        var uni_basic = uniBasic(datos.task_repeat);
                        clearFileDummyMine(uni_basic,datos.task_repeat);
                    }

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
        }else if ( class_ch.indexOf('mode-move')>-1 ) {
            click_uniTime_move=1;
            if (!last_unit_move) {
                _ut.attr('class','drag orange popover-unit-time dia');
                last_unit_move = parent;
                last_uniTime_move=_ut;
            }else if (last_unit_move.attr('id')==$(this).attr('id')) {

            };
            $("div[id^='popover']").remove();
        }

    });

    $('#ch_content').on('click', '#tbl .filter-day', function(){
        var class_ch = $("#ch_content").attr('class');
        var class_td = $(this).attr('class');
        var id_td = $(this).attr('id');
        id_td = id_td.split('_');
        var day_num = id_td[2]-5;
        fechaCol = moment.utc(fechaIni).add('days', day_num).format('DD/MM/YYYY');
        if(class_td.indexOf('editable-yes')>-1){
            if (class_ch.indexOf('mode-add')>-1) {
                var id_temp = 'dummy'+Math.floor((Math.random() * 1000000) + 1);
                $(this).append(createHtmlUnidTime(id_temp));
                showSpinner($("#main_container"));
                $.post(server+'/checklist/sdtAddUnitTime','dummy_id='+id_temp+"&newdate="+fechaCol+"&task_id="+id_td[0]+addPercent(id_td[0]), function(datos){
                    if (datos!='None') {
                        hideSpinner($("#main_container"));
                        $('#tr_'+datos['task_id']).find('.percent').text(datos['task_percent']);
                        $("#"+id_temp).addClass(datos['color']);
                        $("#"+id_temp).attr('id',datos['id']);
                        if (datos['task_change_status']) {
                            $('#tr_'+datos['task_id']).find('.glyphicon-ok-circle').attr('class','glyphicon glyphicon-ok-circle task-wait');
                        };
                        agregarPopover($("#"+datos['id']));
                    };
                }, 'json').fail(function() {
                    console.log("error de conexion");
                });
            }else if ( class_ch.indexOf('mode-move')>-1 ) {
                if(click_uniTime_move==2){
                    if (last_uniTime_move) {
                        $(this).append(last_uniTime_move);
                        var id_td_old = $(last_unit_move).attr('id');
                        id_td_old = id_td_old.split('_');
                        showSpinner($("#main_container"));
                        $.post(server+'/checklist/sdtChangeDayStart','id='+last_uniTime_move.attr('id')+"&newdate="+fechaCol+"&task_id="+id_td[0]+"&task_id_old="+id_td_old[0], function(datos){
                            if (datos!='None') {
                                hideSpinner($("#main_container"));
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
                        last_uniTime_move=null;
                        last_unit_move=null;
                    }
                    click_uniTime_move=0;
                }else{
                    click_uniTime_move=2;
                }
                $("div[id^='popover']").remove();
            }
        }
    });

    $(".cdark_bottom").click(function(){
        $(".day_zoom").removeClass('day_zoom');
        $(this).addClass('day_zoom');
        var id_zo = $(this).attr('id');
        id_zo=id_zo.split('_');
        zoom_day = moment(fechaIni).add('days', id_zo[1]).format('DD/MM/YYYY');
    });

}

var addPercent = function(task_id){
    var perc=1;
    if(tasksRepat[task_id]){
        perc=0;
    }
    return '&perc='+perc;
}

var addUnitTimeFull = function(obj,id_temp){
    var unit_time = $(createHtmlUnidTime(id_temp));
    $(obj).append(unit_time);
    agregarPopover(unit_time);
}
var addUnitTimeNull = function(obj,id_temp,popver){
    var unit_time = $(createHtmlUnidTime(id_temp,popver));
    unit_time = $(unit_time);
    $(obj).append(unit_time);
    agregarPopover(unit_time);
}
var createHtmlUnidTime = function(id,popver){
    var cl = '';
    if(popver){
        cl = 'popover-unit-time';
    }
    return '<div class="drag orange '+cl+'" id="'+id+'">'+
        '<div class="text-center">'+
        '<span class="glyphicon glyphicon-minus circle"></span>'+
        '</div>'+
        '</div>'
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
    if (diffDates(date, fechaFin)>1) {
        return true;
    };
    return false;
}
var NewDate_Complete = function(days){
    var fecha = moment.utc(fechaIni).add('days', days);
    var daystr = fecha.format('dddd');
    var day = fecha.format('D');
    var month = fecha.format('MMMM');
    var year = fecha.format('YYYY');
    return daystr+', ' +day+" de "+month+" del "+year;
}

var NewDate_NumDay = function(days){
    return moment.utc(fechaIni).add('days', days).format('DD');
}
var NewDate_Month_Year = function(days){
    var string = moment.utc(fechaIni).add('days', days).format('MMMM YYYY')
    return string.charAt(0).toUpperCase() + string.slice(1);
}

var getTdIdTask = function(id_tr){
    id_tr = id_tr.split("_");
    return id_tr[0];
}
var htmlHead1 = function(){
    var html='';
    for(var j=1;j<=(ndiff+4);j++){
        if(j==1){
            html+='<td class="only rowhandler fijar1"><div class="row_ch"></div></td>';
        }else if(j==2){
            html+='<td class="only cligth fijar2 range-month" style="background-color:#FFFFFF">'+
                        '<div>'+
                            '<button type="button" class="btn btn-default" id="btn_add_task" style="width:20px;height:20px;" data-toggle="tooltip" data-placement="bottom" title="Nueva Tarea" >'+
                                '<span class="glyphicon glyphicon-plus" style="font-size: 9pt;top: -6px;left: -5px;" data-toggle="modal" data-target="#myModal"></span>'+
                            '</button>'+
                            '<button type="button" class="btn btn-default" id="btn_see_users" style="width:20px;height:20px;" data-toggle="tooltip" data-placement="bottom" title="Ver otros usuarios" >'+
                                '<span class="glyphicon glyphicon-user" style="font-size: 9pt;top: -6px;left: -5px;" data-toggle="modal" data-target="#myModal2"></span>'+
                            '</button>'+
                            'Tareas'+
                        '</div>'+
                    '</td>';
        }else if(j==3){
            html+='<td class="only cligth2 fijar3 range-month" style="background-color:#FFFFFF"></td>';
        }else if(j==4){
            html+='<td class="only cligth2 fijar3 range-month" style="background-color:#FFFFFF">%</td>';
        }else{
            html+='<td class="only cdark cdark_top "></td>';
        }
    }
    return html;
}

var htmlHead2 = function(){
    var html='';
    var day_w = parseInt(day_week);
    var c_w = 'week1';
    var jtoday = moment(dateNewStandard(zoom_day)).diff(moment(fechaIni), 'days') ;
    for(var j=1;j<=(ndiff+4);j++){
        if(j==1){
            html+=  '<td class="only rowhandler fijar1"><div  class="row"></div>'+
                        '<select id="p_sigla" style="width: 20px;height: 10px;" data-toggle="tooltip" data-placement="bottom" title="Siglas">'+
                            '<option value="0">Todo</option>'+
                        '</select>'+
                    '</td>';
        }else if(j==2){
            html+=  '<td class="only cligth fijar2" style="background-color:#FFFFFF">'+
                        '<div class="input-group input-group-sm">'+
                            '<select id="p_respon" style="width: 20px;height: 10px;display:none" data-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filtrar por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable">'+
                                '<option value="0">Todo</option>'+
                            '</select>'+
                            '<select id="p_estado" style="width: 20px;height: 10px;display:none" data-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filtrar por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado de la tarea">'+
                                '<option value="0">Todo</option>'+
                            '</select>'+
                            '<select id="p_direct" style="width: 20px;height: 10px;display:none" data-toggle="tooltip" data-placement="bottom" title="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Filtrar por &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tareas dirigidas">'+
                                '<option value="0">Todo</option>'+
                                '<option value="0">Director</option>'+
                            '</select>'+
                            '<input type="text" id="p_textTa" placeholder="Titulo de la tarea" value="" data-placement="bottom" title="Filtrar por Titulo de &nbsp;las Tareas">'+
                        '</div>'+
                    '</td>';
        }else if(j==3){
            // var select = '<div style="position: relative;"><select id="filter_priority" style="width: 20px;height: 10px;" data-toggle="tooltip" title="Filtrar por prioridad de la Tarea"><option value="Todo" selected="">Todo</option><option value="1">Alta</option><option value="2">Medio</option><option value="3">Bajo</option><option value="4">Informativo</option></select></div>';

            html+='<td class="only cligth2 fijar3" style="background-color:#FFFFFF" id="ch_icons_filter"></td>';
        }else if(j==4){
            html+='<td class="only cligth2 fijar4" style="background-color:#FFFFFF"></td>';
        }else{
            var style='';
            var today='';
            if ((ntoday+5)==j ){
                style='';
                today='today';
            }
            if((jtoday+5)==j){
                today+=' day_zoom';
            }
            c_w = week_color(day_w,c_w);
            html+='<td class="only cdark cdark_bottom '+c_w+' '+today+'"' + style +' id="day_'+(j-5)+'"></td>';
            day_w+=1;
            if(day_w>6){
                day_w=0;
            }
        }
    }
    return html;
}

var week_color = function(day_w,c_w){
    c_w='week1';
    if(day_w==1){
        c_w='week0';
    }
    return c_w;
}

var builtTimeHead1 = function(){
    var dayx=0;
    var objini=null;
    var cellini=null;
    var textdif="";
    var numCells=0;
    $("#tb0 .cdark_top").each(function (index, element) {
        var newTexto = NewDate_Month_Year(dayx);

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
                }else if (parseInt(numCells)<2) {
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
            $(element).addClass('range-month');

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
//    $(objini).tooltip({
//        placement: 'bottom',
//        template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
//    });
}
var builtTimeHead2 = function(){
    var dayx=0;
    var dayx2=moment.utc(fechaIni).day();
    var cellini=null;
    var numtr = $('#tbl >tbody >tr').length;
    $("#ch_icons_filter").append('<div style="position: relative;"><select id="filter_priority" style="width: 20px;height: 10px;" data-toggle="tooltip" title="Filtrar por prioridad de la Tarea"><option value="Todo" selected="">Todo</option><option value="1">Alta</option><option value="2">Medio</option><option value="3">Bajo</option><option value="4">Informativo</option></select></div>');

    $("#tb0 .cdark_bottom").each(function (index, element) {
        var divs = $('<div style="position: relative;"></div>');

        var opcion1= $('<option value="Todo" selected>Todo</option>');
        var opcion2= $('<option value="circle">Cualquier estado</option>');
        var opcion3= $('<option value="minus">Sin resolver</option>');
        var opcion4= $('<option value="ok">Realizado</option>');
        var opcion5= $('<option value="arrow-right">Transferido</option>');
        var opcion6= $('<option value="remove">No realizado</option>');
        var select = $('<select id="p_d_'+(dayx+5)+'" class="filtro_dia" style="width: 20px;height: 10px;">');

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
        $(divc).attr("title",NewDate_Complete(dayx));
//        $(divc).tooltip({
//            placement: 'bottom',
//            template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
//        });
//
//        $(select).attr("data-toggle","tooltip");
//        $(select).attr("title","Filtrar por Estado del dia");
//        $(select).tooltip({
//            placement: 'bottom',
//            template: '<div class="tooltip" style="width: 100px;" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
//        });
        // Encontrar el lunes
        if (dayx2==1) {
        };

        dayx+=1;
        dayx2+=1;
        if (dayx2>6) {
            dayx2=0;
        };
    });
}
var mainTable = function(results){
    var html='';
    var i=0;
    for(var index in results){
        i+=1;
        var data = results[index];
        var leader=false;
        if(data.project){
            if(data.project.user_id == ch_user_id){
                leader=true;
            }
        }else if(ch_user_id != data.user_id){
            leader=true;
        }
        var editable= '';
        var editabletr= 'class="no-editable-tr"';
        if(!data.package){
            if(leader || ch_user_id == data.user_id){
                editable= 'editable-yes';
                editabletr = '';
            }
        }

        if(chEdit){
            editabletr= '';
            editable= 'editable-yes';
        }

        var tr_n =$('<tr id="tr_'+data.id+'" '+editabletr+'></tr>');
        $("#ch-body").append(tr_n);
        var td_n=null;
        for(var j=1;j<=(ndiff+4);j++){
            if(j==1){
                var cls = '';
                var code = '';
                if(data.project){
                    cls = 'redondo';
                    code=data.project.code;
                }else if(data.package){
                    code=data.package.code;
                }
                td_n=  $('<td class="only rowhandler fijar1">' +
                            '<div></div>' +
                            '<div class="row_ch '+cls+'">'+code+'</div>'+
                        '</td>');
            }else if(j==2){
                var style = '';
                if(data.rm){
                    style ='style="color:'+data.rm.c+';background-color:'+data.rm.b_c+';font-family:'+data.rm.f+'"';
                }
                td_n=  $('<td class="only cligth fijar2 ch-name-task" '+style+'>'+
                            data['name']+
                        '</td>');
            }else if(j==3){
                td_n=$('<td class="only cligth2 fijar3"></td>');
                td_n.append(' <span class="glyphicon glyphicon-info-sign task-info" id="info_'+data.id+'"></span>');
                if(!chEdit){
                    td_n.append(' <span class="glyphicon glyphicon-comment task-chat" id="chat_'+data.id+'"></span> ')
                    td_n.append(leaderButtons(data,leader));
                }

            }else if(j==4){
                td_n=$('<td class="only cligth2 fijar3">' +
                    '<div class="text-center percent">'+
                    data['percent']+
                    '</div>'+
                    '</td>');
            }else{
                var new_date = moment(fechaIni).add('days', (j-5)).toDate();
                var n_d_s = moment(new_date).format('YYYY-MM-DD');
                var style='';
                var today='';
                if ((ntoday+5)==j ){
                    style='';
                    today='today';
                }

                var segi= ''; // data[new_date]['f_up']
                var icon= 'glyphicon glyphicon-minus'; // data[new_date]['class-icon']
                td_n=$('<td class="only cdark '+editable +' filter-day '+today+'" '+style+' id="'+data.id+'_'+i+'_'+j+'"></td>');
                if (data.unidTimes){
                    if (data.unidTimes[n_d_s]){
                        var cls = '';
                        if (data.status==1 || leader ){
                            cls='popover-unit-time';
                        }
                        var unid = data.unidTimes[n_d_s];
                        // determinar si es una unidad de tiempo apsada
                        var f_u;
                        if(moment(new_date).diff(moment(fechaTod), 'days')>=0){
                            f_u = segui.class_u.future[unid.f_up];
                        }else{
                            f_u = segui.class_u.past[unid.f_up];
                        }
                        var unit_time = $('<div class="drag orange '+cls+' dia '+f_u+'" id="'+unid.id+'">'+
                                    '<div class="text-center">'+
                                        '<span class="'+segui.icon[unid.f_up]+' circle"></span>'+
                                    '</div>'+
                                '</div>');
                        td_n.append(unit_time)
                        agregarPopover(unit_time);
                    }
                }
            }
            tr_n.append(td_n);
        }
    }
}

var mainTableBlock = function(results,index){
    if(results[index]){
        var i=index+1;
        var data = results[index];
        var leader=false;
        if(data.project){
            if(data.project.user_id == ch_user_id){
                leader=true;
            }
        }else if(ch_user_id != data.user_id){
            leader=true;
        }
        var editable= '';
        var editabletr= 'no-editable-tr';
        if(!data.package){
            if(leader || ch_user_id == data.user_id){
                editable= 'editable-yes';
                editabletr = '';
            }
        }
        if(chEdit){
            editabletr= '';
            editable= 'editable-yes';
        }
        var tr_n =$('<tr id="tr_'+data.id+'"></tr>');
        $("#ch-body").append(tr_n);
        var td_n=null;
        var day_w = parseInt(day_week);
        var c_w = 'week1';
        for(var j=1;j<=(ndiff+4);j++){
            if(j==1){
                var cls = '';
                var code = '';
                if(data.project){
                    cls = 'redondo';
                    code=data.project.code;
                }else if(data.package){
                    code=data.package.code;
                }
                td_n=  $('<td class="only rowhandler fijar1">' +
                    '<div></div>' +
                    '<div class="row_ch '+cls+'">'+code+'</div>'+
                    '</td>');
            }else if(j==2){
                var style = '';
                if(data.rm){
                    style ='style="color:'+data.rm.c+';background-color:'+data.rm.b_c+';font-family:'+data.rm.f+'"';
                }
                td_n=  $('<td class="only cligth fijar2 ch-name-task" '+style+'>'+
                    data['name']+
                    '</td>');
            }else if(j==3){
                td_n=$('<td class="only cligth2 fijar3 '+class_follow_up[data.priority_id]+'"></td>');
                td_n.append(' <span class="glyphicon glyphicon-info-sign task-info" id="info_'+data.id+'"></span>');
                if(!chEdit){
                    var some_msgs = '';
                    if(data.msgs>0){
                        some_msgs = 'chat-msgs';
                    }
                    td_n.append(' <span class="glyphicon glyphicon-comment task-chat '+some_msgs+'" id="chat_'+data.id+'"></span>');
                    td_n.append(leaderButtons(data,leader));
                }
            }else if(j==4){
                td_n=$('<td class="only cligth2 fijar3">' +
                    '<div class="text-center percent">'+
                    data['percent']+
                    '</div>'+
                    '</td>');
            }else{
                var new_date = moment(fechaIni).add('days', (j-5)).toDate();
                var n_d_s = moment(new_date).format('YYYY-MM-DD');
                var style='';
                var today='';
                if ((ntoday+5)==j ){
                    style='';
                    today='today';
                }
                c_w = week_color(day_w,c_w);
                td_n=$('<td class="only cdark '+editable +' filter-day '+c_w+' '+today+' '+editabletr+'" '+style+' id="'+data.id+'_'+i+'_'+j+'"></td>');
                if (data.unidTimes){
                    if (data.unidTimes[n_d_s]){
                        for(var u=0;u<data.unidTimes[n_d_s].length;u++){
                            var cls = '';
                            if (data.status==1 || leader ){
                                cls='popover-unit-time';
                            }
                            var unid = data.unidTimes[n_d_s][u];
                            // determinar si es una unidad de tiempo apsada
                            var f_u;
                            if(moment(new_date).diff(moment(fechaTod), 'days')>=0){
                                f_u = segui.class_u.future[unid.f_up];
                            }else{
                                f_u = segui.class_u.past[unid.f_up];
                            }


                            var unit_time = $('<div class="drag orange '+cls+' dia '+f_u+'" id="'+unid.id+'">'+
                                '<div class="text-center">'+
                                '<span class="'+segui.icon[unid.f_up]+' circle"></span>'+
                                '</div>'+
                                '</div>');
                            td_n.append(unit_time)
                            agregarPopover(unit_time);
                        }
                    }
                }
                day_w+=1;
                if(day_w>6){
                    day_w=0;
                }
            }
            tr_n.append(td_n);
        }
        tr_n.addClass(editabletr);
        domainbody = setTimeout(function () {mainTableBlock(results,index+1)},30);
    }else{
        htmlUnidTimesRepeat(results);
        inicioFiltroCheckList();
        initTaskInfo();
        initTaskChat();
        $('[data-toggle="tooltip"]').tooltip()
    }
}


var htmlUnidTimesRepeat = function(tasks){
    // console.clear();
    for(var index in tasks){
        var data = tasks[index];
        var leader=false;
        if(data.project){
            if(data.project.user_id == ch_user_id){
                leader=true;
            }
        }else if(ch_user_id != data.user_id){
            leader=true;
        }else if(data.status==1){
            leader=true;
        }
        for(var index2 in data.tasksRepeat){
            createRepeat( data.tasksRepeat[index2] , leader );
        }
    }
}

var leaderButtons = function(data,leader){
    var html='';
    if(leader){
        html+=' <span class="glyphicon glyphicon-adjust '+state_task[data.status]+'" ></span>';
    }else if(data.status!=1){
        html+=' <span class="glyphicon glyphicon-warning-sign '+state_task[data.status]+'" id="re_'+data.id+'" data-toggle="tooltip" data-placement="right" title="Click Aqui"></span>';
    }
    return html;
//    if (data.status==0) {
//        if (data['are_you_leader']) {
//            html+='<span class="glyphicon glyphicon-ok-circle '+data['class_state']+'" ></span>';
//        }else{
//            html+='<span class="glyphicon glyphicon-warning-sign '+data['class_state']+'" id="re_'+data['info']['id']+'" data-toggle="tooltip" data-placement="right" title="Click Aqui"></span>';
//        }
//    }else{
//        html+='<span class="glyphicon glyphicon-asterisk '+data['class_state']+'" ></span>';
//    }
//    return html;
}


var buildTable = function(data){
    buildNormalTable(data);

    // Fijar cabecera}
    top_table = $("#tabla").offset();
    top_table = top_table.top;
    $('#tabla').scroll(function(){
        $("#tb0").offset({top:top_table});
    });
}
var buildNormalTable = function(data){
    clearTimeout(domainbody);
    $("#ch-body").html('');
    $("#ch-head1").html(htmlHead1());
    builtTimeHead1();
    $("#ch-head2").html(htmlHead2());
    builtTimeHead2();
    $("#tb0").css( "zIndex", 2 );
    inicioCheckList();
//    mainTable(data.tasks,0)
    mainTableBlock(data.tasks,0)
//    $("#ch-body").html(mainTable(data.tasks));
//    htmlUnidTimesRepeat(data.tasks);
//    inicioCheckList();
//
//    inicioFiltroCheckList();
//
//    initTaskInfo();
//    initTaskChat();

}
var percentRow = function(task_id){
    if(tasksRepat[task_id]){
        var cont =0;
        var conV =0;
        var ut = null;
        var cl = null;
        $("#tr_"+task_id+" .orange").each(function (index, element) {
            ut = $(element).find(".circle");
            cl = ut.attr("class");
            if(cl.indexOf(segui.icon[4])==-1){
                cont+=1;
                if(cl.indexOf(segui.icon[2])>-1){
                    conV+=1;
                }
            }
        });
        var per = $("#tr_"+task_id).find('.percent');
        if(cont>0){
            per.text(parseFloat(conV/cont*100).toFixed(2))
        }else{
            per.text("00.00");
        }
    }
}
var clearFile = function(task_id, day_start){
    day_start = dateNewStandard(day_start);
    var jini = moment.utc(day_start).diff(moment.utc(fechaIni), 'days')+6;
    var rowT = findNRowTable($("#tr_"+task_id));
    for(var j=jini;j<=(ndiff+4);j++){
        var _ut = $("#"+opt.task_id+'_'+rowT+'_'+j).find(".orange");
        if (_ut.length!=0) {
            _ut.remove();
        }
    }
}
var clearFileDummy = function(unid_id){
    if(unitTimes[unid_id]){
        var cont = unitTimes[unid_id].ut.length;
        for(var j=0;j<cont;j++){
            $("#"+unitTimes[unid_id].ut[0]).remove();
            unitTimes[unid_id].ut.splice(0, 1);
        }
    }
}
var clearFileDummyMine = function(unid_id,unid_id_after){
    if(unitTimes[unid_id]){
        var cont = unitTimes[unid_id].ut.length;
        var j_index=0;
        for(var j=0;j<cont;j++){
            if(unitTimes[unid_id].ut[j_index].indexOf('_'+unid_id_after)>-1){
                $("#"+unitTimes[unid_id].ut[j_index]).remove();
                unitTimes[unid_id].ut.splice(j_index, 1);
            }else{
                j_index+=1;
            }
        }
    }
}
var clearFileDummyAfter = function(unid_id, unid_id_after){
    if(unitTimes[unid_id]){
        var cont = unitTimes[unid_id].ut.length;
        var s_j = -1;
        for(var j=0;j<cont;j++){
            if(s_j==-1){
                if(unitTimes[unid_id].ut[j] == unid_id_after){
                    s_j = j+1;
                }
            }else{
                $("#"+unitTimes[unid_id].ut[s_j]).remove();
                unitTimes[unid_id].ut.splice(s_j, 1);
            }
        }
    }
}

var findNRowTable = function (obj){
    var id_i_j = obj.find(".filter-day:eq(0)");
    var id_i_j = id_i_j.attr("id");
    var rowT = id_i_j.split('_');
    return rowT[1];
}
