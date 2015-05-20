/**
 * Created by nesto_000 on 7/04/15.
 */
$(document).ready(function() {
    $('#ch_content').on('click', ".task-info", function(e){
        getinfoTask($(this));
    });
    $('#htd_contenedor').on('click', ".task-info", function(e){
        getinfoTask($(this));
    });
});

var initTaskInfo = function(){
    $(".task-info").click(function(e){
        getinfoTask($(this));
    })
}
var getinfoTask = function(obj){
    // Obtener el id
    var _id = obj.attr('id');
    _id = _id.split('_');
    _id = _id[1];
    $.post(server+'/tasks/getTaskInfo','id='+_id, function(datos){
        if (datos!="None" && datos!="") {
            datos = datos[0];
            $('#sdt-info-task-id').val(datos.tasks.id);
            // Limpiar el contenido
            $("#task-info-content").html('');
            // si proviene de un RM entonces mostrar toda su informacion
            if(datos.registry){
                $("#task-info-content").append($('<h4 class="modal-title">&nbsp;&nbsp;&nbsp;RM</h4>'));
                infoTaskHtml('Numeracion',datos.numbering);
                // transformar la fecha al formato d/m/Y
                var fecha = datos.day.split(' ');
                fecha = fecha[0].split('-');
                fecha = fecha[2]+'/'+fecha[1]+'/'+fecha[0];
                infoTaskHtml('Fecha',fecha);
                if(datos.name){
                    infoTaskHtml('Registro de ',datos.name2+' '+datos.last_name2);
                }else{
                    infoTaskHtml('Registro de ',datos.username2);
                }
                var style = '';
                if(datos.lb_color){
                    style+='style ="color:'+datos.lb_color;
                    style+=';background-color:'+datos.lb_b_color;
                    style+=';font-family:'+datos.lb_font;
                    style+=';font-size:'+datos.lb_size+'"';
                }
                infoTaskHtml('Contenido',datos.registry,style);

            }
            $("#task-info-content").append($('<h4 class="modal-title">&nbsp;&nbsp;&nbsp;Tarea</h4>'));
            infoTaskHtml('Nombre',datos.tasks.name);
            infoTaskHtml('Descripcion',datos.tasks.description);
            infoTaskHtml('Prioridad',datos.priorities.name);
            if(datos.name){
                infoTaskHtml('Responsable',datos.name+' '+datos.last_name);
            }else{
                infoTaskHtml('Responsable',datos.username);
            }

            if (datos.tasks.project_id!=0) {
                $("#task-info-content").append($('<legend></legend>'));
                $("#task-info-content").append($('<h4 class="modal-title">&nbsp;&nbsp;&nbsp;Proyecto</h4>'));
                var html = '<a href="'+server+'/tasks/index/'+datos.projects.team_id+'/'+datos.projects.id+'">'+datos.projects.name+'</a>';
                infoTaskHtml('Nombre',html);
                infoTaskHtml('Siglas',datos.projects.code);
                infoTaskHtml('Descripcion',datos.projects.description);
            };
            if (datos.tasks.package_id!=0) {
                $("#task-info-content").append($('<legend></legend>'));
                $("#task-info-content").append($('<h4 class="modal-title">&nbsp;&nbsp;&nbsp;Checklist</h4>'));
                var html = '<a href="'+server+'/tasks/index/'+datos.packages.team_id+'/0/'+datos.packages.id+'/0">'+datos.packages.name+'</a>';
                infoTaskHtml('Nombre',html);
                infoTaskHtml('Siglas',datos.packages.code);
                infoTaskHtml('Descripcion',datos.packages.description);
            };

            $('#sdt-info-task').modal('show');
        };
    }, 'json');
}
var infoTaskHtml = function (title, content,style){
    if(!style){
        style='';
    }
    var html = 	$('<div class="col-lg-12">'+
        '<div class="col-lg-3">'+
        '<label>'+title+'</label>'+
        '</div>'+
        '<div class="col-lg-9">'+
        '<p '+style+'>'+content+'</p>'+
        '</div>'+
        '</div>');
    $("#task-info-content").append(html);
}