/**
 * Created by nesto_000 on 26/04/15.
 */
"use strict";
var user_sdt = new Array();
var pathnameHTD = window.location.pathname;
$(document).ready(function(){
    pathnameHTD = pathnameHTD.split('/');
    $('#ch_content').on('click', "#btn_add_task", function(e){
        $("#sdt-new-task").modal('show');
    });
    $('#htd_contenedor').on('click', ".add-task-htd", function(e){
        $("#sdt-new-task-fecha").val(moment(new Date()).format('DD/MM/YYYY'));
        $("#sdt-new-task").modal('show');
    });
    $("#sdt-new-task-submit").click(function(){
        var name = $("#sdt-new-task-name").val();
        var description = $("#sdt-new-task-description").val();
        var fecha = $("#sdt-new-task-fecha").val();
        if(pathnameHTD[1]=='htd'){
            fecha = diaAgenda;
        }
        var priority = $("#sdt-new-task-priority").val();
        var delegate = $("#sdt-new-task-delegate").val();
        var project  = $("#sdt-new-task-project").val();
        if (name.trim()!='') {
            $('#sdt-new-task-submit').attr("disabled", true);
            $.post(server+'/tasks/newTask','name='+name+'&description='+description+'&priority='+priority+'&date='+fecha+'&delegate='+delegate+'&project='+project, function(datos){
                if (datos!="None" && datos!="") {
                    $('#sdt-new-task-submit').attr("disabled", false);
                    $('#sdt-new-task').modal('hide');
                    if(pathnameHTD[1]=='checklist'){
                        getTaskData('type='+type_view + parametrosCheclist());
                    }else if(pathnameHTD[1]=='htd'){
                        cargarTareas(diaAgenda,2);
                    }
                };
            }, 'json');
        };
    });


    $("#sdt-new-task-description").keyup(function(){
        enableDisableSubmitNewTask();
    });
    $("#sdt-new-task-name").keyup(function(){
        enableDisableSubmitNewTask();
    });

    iniUsers();

    $("#sdt-new-task-project").html('');
    $("#sdt-new-task-project").append('<option value="0" >--Sin proyecto--</option>');
    $.post(server+'/projects/getProjects', function(datos){
        for(var i=0; i<datos.length;i++){
            $("#sdt-new-task-project").append('<option value="'+datos[i].id+'" >'+datos[i].name+'</option>');
        }
    }, 'json');

    $("#sdt-add-task-repeat").click(function(){
        if ($('input#sdt-add-task-repeat').is(':checked')) {
            createHtmlTaskRepat($("#sdt-add-task-repeat-html"));
        }else{
            $(".sdt-task-repeat").html('');
        }
    });

    $("#sdt-new-task-project").change(function(){
        if($("#sdt-new-task-project").val()>0){
            $.post(server+'/users/getUsersProjects','id='+$("#sdt-new-task-project").val(), function(datos){
                var user_id =datos.user_id;
                var users =datos.users;
                $("#sdt-new-task-delegate").html('');
                if(users.length>0){
                    for(var i=0; i<users.length;i++){
                        var user_a='';
                        if(users[i].id==user_id){
                            user_a='selected';
                        }
                        $("#sdt-new-task-delegate").append('<option value="'+users[i].id+'" '+user_a+'>'+users[i].username+'</option>')
                    }
                    $("#sdt-new-task-submit").prop("disabled", false);
                }else{
                    $("#sdt-new-task-delegate").append('<option value="0" >--Sin usuarios--</option>');
                    $("#sdt-new-task-submit").prop("disabled", true);
                }

            }, 'json');
        }else{
            iniUsers();
        }
    });
});

var enableDisableSubmitNewTask = function(){
    if($("#sdt-new-task-description").val()!="" && $("#sdt-new-task-name").val()!=""){
        $("#sdt-new-task-submit").prop("disabled", false);
    }else{
        $("#sdt-new-task-submit").prop("disabled", true);
    }
}

var iniUsers = function(){
    $.post(server+'/users/getUsers', function(datos){
        var user_id =datos.user_id;
        var users =datos.users;
        $("#sdt-new-task-delegate").html('');
        for(var i=0; i<users.length;i++){
            var user_a='';
            if(users[i].id==user_id){
                user_a='selected';
            }
            $("#sdt-new-task-delegate").append('<option value="'+users[i].id+'" '+user_a+'>'+users[i].username+'</option>')
        }
        enableDisableSubmitNewTask();
    }, 'json');
}