/**
 * Created by nesto_000 on 24/04/15.
 */
/* enable strict mode */
"use strict";
$(document).ready(function(){
    getTaskData('type='+type_view + parametrosCheclist());

    $("#ch_btn_left").click(function(){
        zoom_day = moment.utc(fechaIni).add('days', -1).format('DD/MM/YYYY');
        getTaskData('date=' + zoom_day + '&type='+type_view +parametrosCheclist());
    });
    $("#ch_btn_right").click(function(){
        zoom_day = moment.utc(fechaFin).format('DD/MM/YYYY');
        getTaskData('date=' + zoom_day + '&type='+type_view +parametrosCheclist());
    });
    $("#ch_btn_hoy").click(function(){
        zoom_day = moment.utc(fechaTod).format('DD/MM/YYYY');
        getTaskData('date=' + zoom_day + '&type='+type_view +parametrosCheclist());
    });
    $("#rangoVista").change(function(){
        var godate = zoom_day;
        getTaskData('date=' + godate + '&type='+type_view +parametrosCheclist());
    });

    $("#goToType-type0").click(function(){
        type_view='pend';
        $("#ch-title").text('Tareas Pendientes');
        getTaskData('type=pend' + parametrosCheclist());
    });

    $("#goToType-type1").click(function(){
        type_view='normal';
        $("#ch-title").text('Checklist General');
        getTaskData('type=normal' + parametrosCheclist());
    });

    $("#goToType-type2").click(function(){
        type_view='mypro';
        $("#ch-title").text('Tareas de mis proyectos');
        getTaskData('type=mypro' + parametrosCheclist());
    });

    $("#goToType-type3").click(function(){
        type_view='proje';
        $("#ch-title").text('Tareas de proyectos');
        getTaskData('type=proje' + parametrosCheclist());
    });

    $("#goToType-type4").click(function(){
        type_view='tome';
        $("#ch-title").text('Tareas delegadas a mi');
        getTaskData('type=tome' + parametrosCheclist());
    });

    $("#goToType-type5").click(function(){
        type_view='fromme';
        $("#ch-title").text('Mis tareas delegadas');
        getTaskData('type=fromme' + parametrosCheclist());
    });
    $("#goToType-type6").click(function(){
        type_view='tasks';
        $("#ch-title").text('Mis tareas');
        getTaskData('type=tasks' + parametrosCheclist());
    });

});

var getTaskData = function(parameters){
    showSpinner($("#tabla"));
    $.post(server+'/checklist/sdtChecklistJson', parameters, function(data){
        hideSpinner($("#tabla"));
        if(parameters.indexOf('date')==-1){
            if(moment(fechaTod).diff(moment(dateNewStandard(data.ranges.start)))>=0){
                if(moment(dateNewStandard(data.ranges.end)).diff(moment(fechaTod))>=0){
                    zoom_day = moment(fechaTod).format('DD/MM/YYYY');
                }
            }
        }
        if(!zoom_day){
            zoom_day=data.ranges.start;
        }
        initCHecklist(data);
    }, 'json').fail(function() {
        console.log("error de conexion");
    });
}

var initCHecklist = function(data){
    $("#ch-name-range").text(data.ranges.title);
    $(".ch-give-width").width(data.ranges.diff_days*35+56+56+250+56);
    setRangeStart(dateNewStandard(data.ranges.start));

    setRangeEnd(dateNewStandard(data.ranges.end));
    setUserId(data.user_id);
    setNDiff(data.ranges.diff_days);
    buildTable(data);

}