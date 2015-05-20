{% extends "templates/base.volt" %}
{% block title %} {{ title_view }}{% endblock %}
{% block content %}
    {{ super() }}
    {{ flash.output() }}

    <div class="text-center">
        <h1  id="ch-title">{{ title }}</h1>
        <button type="button" id="demo" class="btn btn-default btn-lg" data-demo="">
            <span class="glyphicon glyphicon-play"></span>
            Tutorial
        </button>
    </div>
    <ul class="nav nav-pills">
        <li role="presentation" id="goToType-type0"><a>Tareas Pendientes</a></li>
        <li role="presentation" id="goToType-type1"><a>Checklist General</a></li>
        <li role="presentation" id="goToType-type2"><a>Mis proyectos</a></li>
        <li role="presentation" id="goToType-type3"><a>Proyectos</a></li>
        <li role="presentation" id="goToType-type4"><a>Delegadas a mi</a></li>
        <li role="presentation" id="goToType-type5"><a>Mis delegadas</a></li>
        <li role="presentation" id="goToType-type6"><a>Mis Tareas</a></li>
    </ul>
    <div id="ch_content"class="">
        <div id="main_container" style="padding:0; margin:0;" class="text-center">
            <div id="drag" style="height:400px">
                <table id="tb" style="width:100%" >
                    <colgroup>
                        <col width="30%"/>
                        <col width="30%"/>
                        <col width="44%"/>
                        <col width="1%"/>
                    </colgroup>
                    <tbody>
                    <tr class="rl">
                        <td class="only cdark" id="ch-name-range">

                        </td>
                        <td class="only cdark">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="ch_btn_left">
                                        <span class="glyphicon glyphicon-chevron-left"></span>
                                    </button>
                                </span>
                                <select id="rangoVista" style="display: inline-block;" class="form-control" >
                                    <option value='week' {% if range == 'week' %}selected{% endif %}>semana</option>
                                    <option value="month" {% if range == 'month' %}selected{% endif %}>mes</option>
                                    <option value="trimestre" {% if range == 'trimestre' %}selected{% endif %}>trimestre</option>
                                </select>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="ch_btn_hoy" data-toggle="tooltip" title="Ir al dia de hoy" data-placement="bottom">
                                        Hoy
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="ch_btn_right">
                                        <span class="glyphicon glyphicon-chevron-right"></span>
                                    </button>
                                 </span>
                            </div>
                        </td>
                        <td class="only cdark">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active" id="ch-mode-1" >
                                    <input type="radio" name="options" autocomplete="off" checked>Normal
                                </label>
                                <label class="btn btn-default" id="ch-mode-2" >
                                    <input type="radio" name="options" autocomplete="off">Agregar
                                </label>
                                <label class="btn btn-default" id="ch-mode-3" >
                                    <input type="radio" name="options" autocomplete="off">Eliminar
                                </label>
                                <label class="btn btn-default" id="ch-mode-4" >
                                    <input type="radio" name="options" autocomplete="off">Mover
                                </label>
                            </div>
                        </td>
                        <td class="only cdark">

                        </td>

                    </tr>
                    </tbody>
                </table>
                <div id="tabla" style="height:350px;position: relative;">
                    <div class="ch-give-width">
                        <div style="position: relative" class="ch-give-width">
                            <table id="tb0" class="table table-condensed ">
                                <tbody>
                                <tr class="rl" id="ch-head1">
                                </tr>
                                <tr class="rl" id="ch-head2" style="font-size: 10pt">
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="position: relative;top: 0px" class="ch-give-width">
                            <table id="tbl" class="table table-condensed">
                                <colgroup>
                                </colgroup>
                                <tbody id="ch-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-transfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Trasladar Tarea</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success" role="alert" id="sdt-success-tranfer" style="display:none">
                        <strong>Bien!</strong> Es una fecha válida para transferir la tarea.
                    </div>
                    <div class="alert alert-danger" role="alert" id="sdt-danger-tranfer"  style="display:none">
                        <strong>Ops!</strong> Recuerda, debe ser una fecha futura.
                    </div>

                    <input type="hidden" name="id_dia" id="sdt-dia" value="" />
                    <input type="hidden" name="fecha_trans" id="fecha_trans" value="" />
                    <input type="hidden" name="obj_json" id="sdt-id_td" value="" />
                    <label for="nombre_tarea">Fecha donde desea trasladar esta tarea</label>
                    <input value="" type="text" class="form-control datepicker" id="sdt-fecha_traslado" placeholder="Fecha de traslado"/>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="sdt-guadar-traslado" data-loading-text="Guardando..." data-dismiss="modal" disabled>Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-new-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Nueva Tarea</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="obj_json" id="sdt-date" value="" />
                    <label for="sdt-new-task-project">Selecione el Proyecto</label>
                    <select class="form-control" id="sdt-new-task-project" >
                    </select>
                    <label for="nombre_tarea">Nombre de la Tarea</label>
                    <input value="" type="text" class="form-control" id="sdt-new-task-name" placeholder="Nombre de la tarea"/>
                    <label for="nombre_tarea">Descripcion</label>
                    <textarea class="form-control" id="sdt-new-task-description"></textarea>
                    <label for="fecha_tarea">Fecha de terminación o entrega</label>
                    <input class="form-control datepicker" id="sdt-new-task-fecha" value="{{ hoy }}"></input>
                    <label for="sdt-new-task-delegate">Delegar a</label>
                    <select class="form-control" id="sdt-new-task-delegate" required="required">
                    </select>
                    <label for="nombre_tarea">Prioridad</label>
                    <select class="form-control" id="sdt-new-task-priority" required="required">
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                        <option value="4">Informativa</option>
                    </select>
                    {#<label class="checkbox-inline">#}
                        {#<input type="checkbox" id="sdt-add-task-repeat" name="sdt-add-task-repeat" value="1"> Agregar Recurrencias#}
                    {#</label>#}
                    {#<div id="sdt-add-task-repeat-html" class="sdt-task-repeat">#}

                    {#</div>#}
                    <div>
                        <button class="btn btn-primary" id="sdt-new-task-submit" style="width:100%" disabled="true">Crear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-actual-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Editar Tarea</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sdt-actual-task-id" value="" />
                    <label for="nombre_tarea">Nombre de la Tarea</label>
                    <input value="" type="text" class="form-control" id="sdt-actual-task-name" placeholder="Nombre de la tarea"/>
                    <label for="nombre_tarea">Descripcion</label>
                    <textarea class="form-control" id="sdt-actual-task-description"></textarea>
                    <label for="nombre_tarea">Prioridad</label>
                    <select class="form-control" id="sdt-actual-task-priority" required="required">
                        <option value="1">Alta</option>
                        <option value="2">Media</option>
                        <option value="3">Baja</option>
                        <option value="4">Informativa</option>
                    </select>
                    <div>
                        <button class="btn btn-primary" id="sdt-actual-task-submit" style="width:100%" >Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-info-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    <h4 class="modal-title" id="myModalLabel">Informacion</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sdt-info-task-id" value="" />
                    <div class="row" id="task-info-content">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModalRespTarea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirma realizar esta tarea?</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ch-RespTareaId" value="0" />
                    <input type="hidden" id="ch-respuesta" value="1" />
                    <div class="form-group col-md-12">
                        <div class="btn-group btn-group-justified" role="group"  data-toggle="buttons">
                            <label class="btn btn-default" id="respuesta_si">
                                <input class="importa" type="radio" name="respues" value="1"> Sí
                            </label>
                            <label class="btn btn-default" id="respuesta_no">
                                <input class="importa" type="radio" name="respues" value="3"> No
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <textarea id="ch-resp-comentario" class="form-control" placeholder="Al rechazar la tarea debe justifiar el porque" style="display:none"></textarea>
                    </div>
                    <div>
                        <button class="form-control" id="submit_respuesta">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-chat-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <span class="glyphicon glyphicon-comment"></span> <span id="sdt-chat-title"></span>
                </div>
                <div class="modal-body" id="panel-body">
                    <ul class="chat" id="panel-body-chat">

                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input type="hidden" id="sdt-task-chat"  value="0"/>
                        <input id="btn-input-chat" type="text" class="form-control input-sm" placeholder="Escriba su mensaje aqui..." />
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                Enviar</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sdt-repeat-task" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    Repetir
                </div>
                <div class="modal-body sdt-task-repeat" id="sdt-repeat-task-html">

                </div>
            </div>
        </div>
    </div>
{% endblock %}